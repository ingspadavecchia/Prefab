<?php
declare(strict_types=1);

namespace Neighborhoods\Radar\V1\MV\Blip\Update;

use Neighborhoods\Radar\V1\MV\Blip\UpdateInterface;

/** @codeCoverageIgnore */
interface FactoryInterface
{
    public function create(): UpdateInterface;
}
