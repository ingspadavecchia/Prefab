<?php
declare(strict_types=1);

namespace Neighborhoods\Prefab\Repository\Generator;

use Neighborhoods\Prefab\Repository\GeneratorInterface;

/** @codeCoverageIgnore */
class Factory implements FactoryInterface
{
    use AwareTrait;

    public function create() : GeneratorInterface
    {
        return clone $this->getRepositoryGenerator();
    }
}
