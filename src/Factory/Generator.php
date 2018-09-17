<?php
declare(strict_types=1);

namespace Neighborhoods\Prefab\Factory;

use Neighborhoods\Prefab\ClassSaverInterface;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Reflection\ClassReflection;

class Generator implements GeneratorInterface
{
    protected $namespace;
    protected $version;
    protected $generator;
    protected $classSaver;

    protected const CLASS_NAME = 'Factory';

    public function generate() : GeneratorInterface
    {
        $this->setGenerator();

        $this->getGenerator()->setNamespaceName($this->getNamespace());
        $this->getGenerator()->addTrait('AwareTrait');
        $this->getGenerator()->setImplementedInterfaces([$this->getNamespace() . '\FactoryInterface']);
        $this->getGenerator()->setName(self::CLASS_NAME);

        $this->replaceReturnTypePlaceHolders();

        $file = new FileGenerator();
        $file->setClass($this->getGenerator());

        $builtFile = $this->replaceEntityPlaceholders($file->generate());

        $this->getClassSaver()
            ->setNamespace($this->getNamespace())
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
                $method->setReturnType($this->getNamespace() . 'Interface');
            }
        }
    }

    protected function replaceEntityPlaceholders($fileContent) : string
    {
        $fileContent = str_replace('DAONAMEPLACEHOLDER', $this->getNamespace(), $fileContent);
        $methodVarName = implode('', explode('\\', $this->getNamespace()));
        $fileContent = str_replace('DAOVARNAMEPLACEHOLDER', $methodVarName, $fileContent);
        return $fileContent;
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

    protected function getNamespace() : string
    {
        if ($this->namespace === null) {
            throw new \LogicException('Generator namespace has not been set.');
        }
        return $this->namespace;
    }

    public function setNamespace(string $namespace) : GeneratorInterface
    {
        if ($this->namespace !== null) {
            throw new \LogicException('Generator namespace is already set.');
        }
        $this->namespace = $namespace;
        return $this;
    }

    protected function getClassSaver() : ClassSaverInterface
    {
        if ($this->classSaver === null) {
            throw new \LogicException('Generator classSaver has not been set.');
        }
        return $this->classSaver;
    }

    public function setClassSaver(ClassSaverInterface $classSaver) : GeneratorInterface
    {
        if ($this->classSaver !== null) {
            throw new \LogicException('Generator classSaver is already set.');
        }
        $this->classSaver = $classSaver;
        return $this;
    }
}
