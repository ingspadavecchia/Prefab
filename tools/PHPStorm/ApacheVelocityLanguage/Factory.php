#set( $unqualifiedClassName = "$NAMESPACE.substring($NAMESPACE.lastIndexOf('\')).substring(1)" )
#set( $isArrayFactory = "$NAMESPACE.endsWith('Array')" )
<?php
declare(strict_types=1);

#if (${NAMESPACE})
namespace ${NAMESPACE};

#end
use ${NAMESPACE}Interface;

class Factory implements FactoryInterface
{
    use AwareTrait;

    public function create(): ${unqualifiedClassName}Interface
    {
        #if ( $isArrayFactory == "true" )
        return ${DS}this->_get${targetClassVariable}()->getArrayCopy();
        #else
        return clone ${DS}this->_get${targetClassVariable}();
        #
    }
}
