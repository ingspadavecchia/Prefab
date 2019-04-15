<?php
declare(strict_types=1);

namespace Neighborhoods\ReplaceThisWithTheNameOfYourProduct\Prefab5\HTTPBuildableDirectoryMap\ContainerBuilder;

use Neighborhoods\ReplaceThisWithTheNameOfYourProduct\Prefab5\Opcache;
use Neighborhoods\ReplaceThisWithTheNameOfYourProduct\Prefab5\Opcache\HTTPBuildableDirectoryMap\BuildableDirectoryFileNotFound;
use Neighborhoods\ReplaceThisWithTheNameOfYourProduct\Prefab5\HttpBuildableDirectoryMap\ContainerBuilder;
use Neighborhoods\ReplaceThisWithTheNameOfYourProduct\Prefab5\Protean\Container\Builder;

class Prebuilder implements PrebuilderInterface
{
    public function prebuildContainers() : PrebuilderInterface
    {
        try {
            $httpBuildableDirectoryMap = (new Opcache\HTTPBuildableDirectoryMap())->getBuildableDirectoryMap();
        } catch (BuildableDirectoryFileNotFound\Exception $exception) {
            // No directory map file found. Nothing to build
            return $this;
        }

        foreach ($httpBuildableDirectoryMap as $key => $values) {
            $proteanContainerBuilder = new Builder();
            $proteanContainerBuilder->getFilesystemProperties()->setRootDirectoryPath(__DIR__ . '/../../../../');

            $containerBuilder = (new ContainerBuilder())
                ->setProteanContainerBuilder($proteanContainerBuilder)
                ->setDirectoryGroup($key)
                ->setBuildableDirectoryMap([$key => $values])
                ->getContainerBuilder();

            $containerBuilder->build();
        }

        return $this;
    }
}
