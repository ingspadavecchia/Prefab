<?php
declare(strict_types=1);

namespace Neighborhoods\Prefab\Builder;

use Symfony\Component\Finder\SplFileInfo;

interface GeneratorInterface
{
    public function generate();
}
