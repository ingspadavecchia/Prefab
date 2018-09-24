<?php
declare(strict_types=1);

namespace Neighborhoods\Prefab\Console;

use Neighborhoods\Prefab\Actor\AwareTrait;
use Neighborhoods\Prefab\Actor\Builder;
use Neighborhoods\Prefab\Actor\Factory;
use Neighborhoods\Prefab\Actor\Map;
use Neighborhoods\Prefab\Actor\MapInterface;
use Neighborhoods\Prefab\Actor\Repository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class GenerateFabCommand extends Command
{
    use AwareTrait\Generator\Factory\AwareTrait;
    use Builder\Generator\Factory\AwareTrait;
    use Factory\Generator\Factory\AwareTrait;
    use Map\Generator\Factory\AwareTrait;
    use MapInterface\Generator\Factory\AwareTrait;
    use Repository\Generator\Factory\AwareTrait;
    use GeneratorMeta\Factory\AwareTrait;

    const FORWARD_SLASH = '/';
    const BACKSLASH = '\\';

    /** @var array */
    protected $buildPlan = [];
    /** @var string */
    protected $daoName;
    /** @var string */
    protected $daoNamespace;
    /** @var string */
    protected $daoRelativePath;
    /** @var string */
    protected $fabLocation;
    /** @var string */
    protected $srcLocation;

    protected function configure()
    {
        $this->setName('gen:fab')
            ->setDescription('Generate Protean Machinery');

        // We should probably do something better than this
        $this->srcLocation = __DIR__ . '/../../../../../src';
        $this->fabLocation = __DIR__ . '/../../../../../fab';
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Assembling Prefab build plan.');
        $this->assembleBuildPlan();
        $output->writeln('Build plan complete.');

        $output->writeln('Generating prefab machinery.');
        $this->generatePrefab();
        $output->writeln('Prefab generation complete.');

        return $this;
    }

    protected function assembleBuildPlan()
    {
        $daoAnnotationPattern = '#@neighborhoods\\\prefab:DAO\n#s';

        $finder = new Finder();
        $daos = $finder->files()->contains($daoAnnotationPattern)->in($this->srcLocation);

        /** @var SplFileInfo $dao */
        foreach ($daos as $dao) {
            $daoName = $dao->getBasename('.php');
            $daoFilePath = $this->fabLocation . self::FORWARD_SLASH . $dao->getRelativePath();
            $daoNamespace = $this->getDaoNamespaceFromFile($dao);

            $daoMeta = $this->getConsoleGeneratorMetaFactory()->create();
            $daoMeta->setDaoName($daoName);
            $daoMeta->setActorNamespace($daoNamespace);
            $daoMeta->setActorFilePath($daoFilePath);

            $this->addDaoToPlan($daoMeta);
        }

        return $this;
    }

    protected function generatePrefab()
    {
        $generatorList = $this->getBuildPlan();
        /** @var GeneratorInterface $generator */
        foreach ($generatorList as $generator) {
            $generator->generate();
        }

        return $this;
    }

    protected function generateDaoMeta(SplFileInfo $dao)
    {
        $this->setDaoName($dao->getBasename('.php'));
        $this->setDaoRelativePath($dao->getRelativePath());
        $this->setDaoNamespace($this->getDaoNamespaceFromFile($dao));

        return $this;
    }

    protected function unsetDaoMeta()
    {
        $this->unsetDaoName();
        $this->unsetDaoRelativePath();
        $this->unsetDaoNamespace();

        return $this;
    }

    protected function getDaoNamespaceFromFile(SplFileInfo $dao)
    {
        preg_match('#namespace (.*?);#s', $dao->getContents(), $namespace);

        return $namespace[1];
    }

    protected function getPrefabAnnotations(SplFileInfo $file): array
    {
        preg_match_all('#@neighborhoods\\\prefab:(.*?)\n#s', $file->getContents(), $annotations);
        return $annotations[1];
    }

    protected function addDaoToPlan(GeneratorMetaInterface $daoMeta): self
    {
        $filePath = $this->fabLocation . self::FORWARD_SLASH . $this->getDaoRelativePath();

//        $daoMeta = new GeneratorMeta();
//        $daoMeta->setActorNamespace($this->getDaoNamespace());
//        $daoMeta->setActorFilepath($filePath);

        /** @todo DAO generator logic + Service.yml */

        $nextLevelNamespace = $daoMeta->getActorNamespace() . self::BACKSLASH . $daoMeta->getDaoName();
        $nextLevelFilePath = $filePath . self::FORWARD_SLASH . $daoMeta->getDaoName();

        $nextLevelMeta = $this->getConsoleGeneratorMetaFactory()->create();
        $nextLevelMeta->setActorNamespace($nextLevelNamespace);
        $nextLevelMeta->setActorFilepath($nextLevelFilePath);
        $nextLevelMeta->setDaoName($this->getDaoName());

        //$this->addServiceToPlan($daoMeta);
//        $this->addAwareTraitToPlan($nextLevelMeta);
//        $this->addFactoryToPlan($nextLevelMeta);
        $this->addMapToPlan($nextLevelMeta);
//        $this->addRepositoryToPlan($nextLevelMeta);

        return $this;
    }

    protected function addAwareTraitToPlan(GeneratorMetaInterface $meta): self
    {
        $awareTraitGenerator = $this->getAwareTraitGeneratorFactory()->create();
        $awareTraitGenerator->setMeta($meta);
        $this->appendGeneratorToBuildPlan($awareTraitGenerator);

        return $this;
    }

    protected function addBuilderToPlan(GeneratorMetaInterface $builderMeta): self
    {
        $builderGenerator = $this->getBuilderGeneratorFactory()->create();
        $builderGenerator->setMeta($builderMeta);
        $this->appendGeneratorToBuildPlan($builderGenerator);

        $nextLevelMeta = $this->getNextLevelMeta($builderGenerator);

        $this->addAwareTraitToPlan($nextLevelMeta);
        $this->addFactoryToPlan($nextLevelMeta);

        return $this;
    }

    protected function addBuilderInterfaceToPlan(GeneratorMetaInterface $builderInterfaceMeta): self
    {


        return $this;
    }

    protected function addFactoryToPlan(GeneratorMetaInterface $factoryMeta): self
    {
        $factoryGenerator = $this->getFactoryGeneratorFactory()->create();
        $factoryGenerator->setMeta($factoryMeta);
        $this->appendGeneratorToBuildPlan($factoryGenerator);

        $nextLevelMeta = $this->getNextLevelMeta($factoryGenerator);

        $this->addAwareTraitToPlan($nextLevelMeta);

        return $this;
    }

    protected function addFactoryInterfaceToPlan(GeneratorMetaInterface $factoryMeta): self
    {

        return $this;
    }

    protected function addHandlerToPlan(GeneratorMetaInterface $handlerMeta): self
    {

        return $this;
    }

    protected function addHandlerInterfaceToPlan(GeneratorMetaInterface $handlerInterfaceMeta): self
    {

        return $this;
    }

    protected function addMapToPlan(GeneratorMetaInterface $mapMeta): self
    {
        $mapGenerator = $this->getMapGeneratorFactory()->create();
        $mapGenerator->setMeta($mapMeta);
        $this->appendGeneratorToBuildPlan($mapGenerator);
        $this->addMapInterfaceToPlan($mapMeta);

        $nextLevelMeta = $this->getNextLevelMeta($mapGenerator);
        $this->addAwareTraitToPlan($nextLevelMeta);
        $this->addBuilderToPlan($nextLevelMeta);
        $this->addFactoryToPlan($nextLevelMeta);

        return $this;
    }

    protected function addMapInterfaceToPlan(GeneratorMetaInterface $mapInterfaceMeta): self
    {
        $mapInterfaceGenerator = $this->getMapInterfaceGeneratorFactory()->create();
        $mapInterfaceGenerator->setMeta($mapInterfaceMeta);
        $this->appendGeneratorToBuildPlan($mapInterfaceGenerator);

        return $this;
    }


    protected function addRepositoryToPlan(GeneratorMetaInterface $repositoryMeta): self
    {
        $repositoryGenerator = $this->getRepositoryGeneratorFactory()->create();
        $repositoryGenerator->setMeta($repositoryMeta);
        $this->appendGeneratorToBuildPlan($repositoryGenerator);

        $nextLevelMeta = $this->getNextLevelMeta($repositoryGenerator);

        $this->addAwareTraitToPlan($nextLevelMeta);

        return $this;
    }

    protected function addRepositoryInterfaceToPlan(GeneratorMetaInterface $repositoryInterfaceMeta): self
    {

    }

    protected function addServiceToPlan(GeneratorMetaInterface $serviceMeta): self
    {
        /** @todo Service generator logic */

        return $this;
    }

    protected function getNextLevelMeta(GeneratorInterface $parentGenerator): GeneratorMetaInterface
    {
        $parentMeta = $parentGenerator->getMeta();
        $actorName = $parentGenerator->getActorName();
        $nextLevelNamespace = $parentMeta->getActorNamespace() . self::BACKSLASH . $actorName;
        $nextLevelFilePath = $parentMeta->getActorFilePath() . self::FORWARD_SLASH . $actorName;

        $nextLevelMeta = $this->getConsoleGeneratorMetaFactory()->create();
        $nextLevelMeta->setActorNamespace($nextLevelNamespace);
        $nextLevelMeta->setActorFilepath($nextLevelFilePath);
        $nextLevelMeta->setDaoName($this->getDaoName());

        return $nextLevelMeta;
    }

    protected function appendGeneratorToBuildPlan(GeneratorInterface $generator)
    {
        $this->buildPlan[] = $generator;
        return $this;
    }

    protected function getBuildPlan(): array
    {
        if ($this->buildPlan === null) {
            throw new \LogicException('GenerateFabCommand buildPlan has not been set.');
        }
        return $this->buildPlan;
    }

    protected function getDaoName(): string
    {
        if ($this->daoName === null) {
            throw new \LogicException('GenerateFabCommand daoName has not been set.');
        }
        return $this->daoName;
    }

    protected function setDaoName(string $daoName)
    {
        if ($this->daoName !== null) {
            throw new \LogicException('GenerateFabCommand daoName is already set.');
        }
        $this->daoName = $daoName;
        return $this;
    }

    protected function unsetDaoName()
    {
        if ($this->daoName === null) {
            throw new \LogicException('GenerateFabCommand daoName has not been set.');
        }
        $this->daoName = null;
        return $this;
    }

    protected function getDaoNamespace(): string
    {
        if ($this->daoNamespace === null) {
            throw new \LogicException('GenerateFabCommand daoNamespace has not been set.');
        }
        return $this->daoNamespace;
    }

    protected function setDaoNamespace(string $daoNamespace)
    {
        if ($this->daoNamespace !== null) {
            throw new \LogicException('GenerateFabCommand daoNamespace is already set.');
        }
        $this->daoNamespace = $daoNamespace;
        return $this;
    }

    protected function unsetDaoNamespace()
    {
        if ($this->daoNamespace === null) {
            throw new \LogicException('GenerateFabCommand daoNamespace has not been set.');
        }
        $this->daoNamespace = null;
        return $this;
    }

    protected function getDaoRelativePath(): string
    {
        if ($this->daoRelativePath === null) {
            throw new \LogicException('GenerateFabCommand daoRelativePath has not been set.');
        }
        return $this->daoRelativePath;
    }

    protected function setDaoRelativePath(string $daoRelativePath)
    {
        if ($this->daoRelativePath !== null) {
            throw new \LogicException('GenerateFabCommand daoRelativePath is already set.');
        }
        $this->daoRelativePath = $daoRelativePath;
        return $this;
    }

    protected function unsetDaoRelativePath()
    {
        if ($this->daoRelativePath === null) {
            throw new \LogicException('GenerateFabCommand daoRelativePath has not been set.');
        }
        $this->daoRelativePath = null;
        return $this;
    }
}
