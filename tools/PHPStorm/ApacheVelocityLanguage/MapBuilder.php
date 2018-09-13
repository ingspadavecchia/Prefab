#set( $unqualifiedClassName = "$NAMESPACE.substring($NAMESPACE.lastIndexOf('\')).substring(1)" )
#set( $isArrayFactory = "$NAMESPACE.endsWith('Array')")
#set( $isMapFactory = "$NAMESPACE.endsWith('Map')")
#set($truncatedClassPath = "")
#parse("truncated classpath")
#set( $elementType = "$lastPartOfNamespace" )
#set( $daoName = "$unqualifiedClassName.toLowerCase()" )
<?php
declare(strict_types=1);

#if (${NAMESPACE})
namespace ${NAMESPACE};

#end
use ${NAMESPACE}Interface;

class Builder implements BuilderInterface
{
    use Factory\AwareTrait;
    use // Replace with reference to non-Map version of Builder\Factory\AwareTrait;
    /** @var array */
    protected ${DS}records;

    public function build(): ${unqualifiedClassName}Interface
    {
        ${DS}${daoName} = ${DS}this->get${truncatedClassPath}Factory()->create();
        foreach($this->getRecords() as $record) {
            $builder = $this->getDORClassBuilderFactory()->create(); // replace DORClass w/ e.g. DOR0Listing, MV1Area
            $item = $builder->setRecord($record)->build();
            $map[$item->getId()] = $item;
        }

        return ${DS}${daoName};
    }

    protected function getRecords(): array
    {
        if (${DS}this->records === null) {
            throw new \LogicException('Builder records has not been set.');
        }

        return ${DS}this->records;
    }

    public function setRecords(array ${DS}records): BuilderInterface
    {
        if (${DS}this->records !== null) {
            throw new \LogicException('Builder records is already set.');
        }

        ${DS}this->records = ${DS}records;

        return ${DS}this;
    }
}
