#set( $unqualifiedClassName = "$NAMESPACE.substring($NAMESPACE.lastIndexOf('\')).substring(1)" )
#set( $lowerCaseUnqualifiedClassName = "$unqualifiedClassName.substring(0,1).toLowerCase()$unqualifiedClassName.substring(1)" )
<?php
declare(strict_types=1);

#if (${NAMESPACE})
namespace ${NAMESPACE};

use ${NAMESPACE}Interface;

#end
class Iterator implements IteratorInterface
{
    protected ${DS}generator;

    public function next() : void
    {
        ${DS}this->getGenerator()->next();
    }

    public function current() : ${unqualifiedClassName}Interface
    {
        return ${DS}this->_assertValidArrayItemType(
            ${DS}this->getGenerator()->current()
        );
    }

    public function valid() : bool
    {
        return ${DS}this->getGenerator()->valid();
    }

    public function rewind() : void
    {
        ${DS}this->getGenerator()->rewind();
    }

    public function key()
    {
        return ${DS}this->getGenerator()->key();
    }

    protected function _assertValidArrayItemType(${unqualifiedClassName}Interface ${DS}$lowerCaseUnqualifiedClassName) : ${unqualifiedClassName}Interface
    {
        return ${DS}$lowerCaseUnqualifiedClassName;
    }

    protected function getGenerator() : \Iterator
    {
        if (${DS}this->generator === null) {
            throw new \LogicException('`generator` has not been set.');
        }
        return ${DS}this->generator;
    }

    public function setGenerator(\Iterator ${DS}generator) : IteratorInterface
    {
        if (${DS}this->generator !== null) {
            throw new \LogicException('`generator` already set.');
        }
        ${DS}this->generator = ${DS}generator;
        return ${DS}this;
    }
}
