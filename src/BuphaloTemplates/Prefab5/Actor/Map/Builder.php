<?php

namespace Neighborhoods\Buphalo\Template\Actor\Map;

use Neighborhoods\Buphalo\Template\Actor\MapInterface;

class Builder implements BuilderInterface
{

    use \Neighborhoods\Buphalo\Template\Actor\Map\Factory\AwareTrait;
    use \Neighborhoods\Buphalo\Template\Actor\Builder\Factory\AwareTrait;

    /**
     * @var array
     */
    protected $records = null;

    public function build() : MapInterface
    {
        $map = $this->getActorMapFactory()->create();
        foreach ($this->getRecords() as $record) {
            $builder = $this->getActorBuilderFactory()->create();
            $item = $builder->setRecord($record)->build();
            /** @neighborhoods-buphalo:annotation-processor Neighborhoods\Prefab\AnnotationProcessor\Actor\Map\Builder-identity-field
            $map[]
        */ = $item;
        }

        return $map;
    }

    public function buildForInsert() : MapInterface
    {
        $map = $this->getActorMapFactory()->create();
        foreach ($this->getRecords() as $index => $record) {
            $builder = $this->getActorBuilderFactory()->create();
            $item = $builder->setRecord($record)->buildForInsert();
            $itemIndex = /** @neighborhoods-buphalo:annotation-processor Neighborhoods\Prefab\AnnotationProcessor\Actor\Map\Builder-identity-field-ternary
            $index
             */;
            $map[$itemIndex] = $item;
        }

        return $map;
    }

    protected function getRecords() : array
    {
        if ($this->records === null) {
            throw new \LogicException('Builder records has not been set.');
        }

        return $this->records;
    }

    public function setRecords(array $records) : BuilderInterface
    {
        if ($this->records !== null) {
            throw new \LogicException('Builder records is already set.');
        }

        $this->records = $records;

        return $this;
    }
}

