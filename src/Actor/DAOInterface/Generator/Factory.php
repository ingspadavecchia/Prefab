<?php
declare(strict_types=1);

namespace Neighborhoods\Prefab\Actor\DAOInterface\Generator;

use Neighborhoods\Prefab\Actor\DAOInterface\GeneratorInterface;

/** @codeCoverageIgnore */
class Factory implements FactoryInterface
{
    use AwareTrait;

    public function create() : GeneratorInterface
    {
        return clone $this->getActorDAOInterfaceGenerator();
    }
}
