<?php
declare(strict_types=1);

namespace Neighborhoods\Prefab\Repository;

use Neighborhoods\Prefab\Console\GeneratorInterface;
use Neighborhoods\Prefab\Console\GeneratorMetaInterface;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Reflection\ClassReflection;
use Neighborhoods\Prefab\ClassSaver;

class Generator implements GeneratorInterface
{
    use ClassSaver\AwareTrait;

    public const CLASS_NAME = 'Repository';

    protected $generator;
    protected $varName;
    protected $projectName;
    protected $classSaver;
    /** @var GeneratorMetaInterface */
    protected $meta;

    public function generate() : GeneratorInterface
    {
        $this->setGenerator();

        $this->getGenerator()->setNamespaceName($this->getMeta()->getActorNamespace());
        $this->getGenerator()->setImplementedInterfaces([$this->getMeta()->getActorNamespace() . '\RepositoryInterface']);
        $this->getGenerator()->setName(self::CLASS_NAME);

        $this->getGenerator()->addTraits(
            [
                'Neighborhoods\\' . $this->getProjectName() . '\Doctrine\DBAL\Connection\Decorator\Repository\AwareTrait',
                'Neighborhoods\\' . $this->getProjectName() . '\SearchCriteria\Doctrine\DBAL\Query\QueryBuilder\Builder\Factory\AwareTrait',
                'Neighborhoods\\' . $this->getProjectName() . '\SearchCriteriaInterface',
            ]
        );

        $file = new FileGenerator();
        $file->setClass($this->getGenerator());

        $builtFile = $this->replaceEntityPlaceholders($file->generate());

        $this->getClassSaver()
            ->setNamespace($this->getMeta()->getActorNamespace())
            ->setClassName(self::CLASS_NAME)
            ->setGeneratedClass($builtFile)
            ->saveClass();

        return $this;
    }

    protected function replaceReturnTypePlaceHolders()
    {
        $methods = $this->getGenerator()->getMethods();

        foreach ($methods as $method) {
            $returnType = $method->getReturnType();
            if ($returnType && strpos($returnType->generate(), 'DAONAMEPLACEHOLDERInterface')) {
                $method->setReturnType($this->getMeta()->getActorNamespace() . 'Interface');
            }
        }
    }

    protected function replaceEntityPlaceholders($fileContent) : string
    {
        $namespace = $this->getMeta()->getActorNamespace();
        $fileContent = str_replace('DAONAMEPLACEHOLDER', $namespace, $fileContent);
        $methodVarName = implode('', explode('\\', $namespace));
        $fileContent = str_replace('DAOVARNAMEPLACEHOLDER', $methodVarName, $fileContent);
        $fileContent = str_replace('PROJECTNAMEPLACEHOLDER', $this->getProjectName(), $fileContent);
        $fileContent = str_replace('NAMESPACEPLACEHOLDER', $namespace, $fileContent);

        return $fileContent;
    }

    protected function getProjectName() : string
    {
        if ($this->projectName === null) {
            $this->projectName = explode('\\', $this->getMeta()->getActorNamespace())[1];
        }
        return $this->projectName;
    }

    protected function setGenerator() : GeneratorInterface
    {
        $template = new ClassReflection(Template::class);
        $this->generator = ClassGenerator::fromReflection($template);
        return $this;
    }

    protected function getGenerator() : ClassGenerator
    {
        if ($this->generator === null) {
            throw new \LogicException('Generator generator has not been set');
        }

        return $this->generator;
    }

    public function getMeta(): GeneratorMetaInterface
    {
        if ($this->meta === null) {
            throw new \LogicException('Generator meta has not been set.');
        }
        return $this->meta;
    }

    public function setMeta(GeneratorMetaInterface $meta): GeneratorInterface
    {
        if ($this->meta !== null) {
            throw new \LogicException('Generator meta is already set.');
        }
        $this->meta = $meta;
        return $this;
    }

    public function getActorName(): string
    {
        return self::CLASS_NAME;
    }
}
