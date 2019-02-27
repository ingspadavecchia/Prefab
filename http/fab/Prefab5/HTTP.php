<?php
declare(strict_types=1);

namespace Neighborhoods\ReplaceThisWithTheNameOfYourProduct\Prefab5;

use Neighborhoods\ReplaceThisWithTheNameOfYourProduct\Prefab5\Opcache\HTTPBuildableDirectoryMap;
use Neighborhoods\ReplaceThisWithTheNameOfYourProduct\Prefab5\Protean;
use Zend\Expressive\Application;
use Neighborhoods\ReplaceThisWithTheNameOfYourProduct\Prefab5\Opcache\HTTPBuildableDirectoryMap\InvalidDirectory;
use Neighborhoods\ReplaceThisWithTheNameOfYourProduct\Prefab5\Opcache\HTTPBuildableDirectoryMap\BuildableDirectoryFileNotFound;

class HTTP implements HTTPInterface
{
    use Protean\Container\Builder\AwareTrait;

    protected const HTTP_CODE_BAD_REQUEST = 400;
    protected const HTTP_CODE_INTERNAL_ERROR = 500;
    protected const YAML_KEY_BUILDABLE_DIRECTORIES = 'buildable_directories';
    protected const YAML_KEY_WELCOME_BASKETS = 'welcome_baskets';
    protected const YAML_KEY_APPENDED_PATHS = 'appended_paths';

    public function respond() : HTTPInterface
    {
        try {
            $this->configureContainerBuilder();

            $application = $this->getProteanContainerBuilder()->build()->get(Application::class);
            $application->run();
        } catch (InvalidDirectory\Exception | HTTP\Exception $exception) {
            http_response_code(self::HTTP_CODE_BAD_REQUEST);
            (new NewRelic())->noticeThrowable($exception);
        } catch (\Throwable $throwable) {
            http_response_code(self::HTTP_CODE_INTERNAL_ERROR);
            (new NewRelic())->noticeThrowable($throwable);
        }

        return $this;
    }

    protected function configureContainerBuilder() : HTTPInterface
    {
        try {
            $httpBuildableDirectoryMap = (new HTTPBuildableDirectoryMap())->getBuildableDirectoryMap();
        } catch (BuildableDirectoryFileNotFound\Exception $exception) {
            // No YAML file found. Build full container
            $this->getProteanContainerBuilder()->buildZendExpressive();
            $this->getProteanContainerBuilder()->setContainerName('HTTP');
            return $this;
        }

        $urlRoot = $this->getUrlRoot();

        $this->getProteanContainerBuilder()->setContainerName('HTTP_' . $urlRoot);

        if (!isset($httpBuildableDirectoryMap[$urlRoot])) {
            throw (new InvalidDirectory\Exception)->setCode(InvalidDirectory\Exception::CODE_INVALID_DIRECTORY);
        }

        $this->getProteanContainerBuilder()->buildZendExpressive();

        $routeBuildableDirectories = $httpBuildableDirectoryMap[$urlRoot];

        $this->addBuildableDirectories($routeBuildableDirectories);
        $this->addWelcomeBaskets($routeBuildableDirectories);
        $this->addAppendedPaths($routeBuildableDirectories);

        return $this;
    }

    protected function getUrlRoot() : string
    {
        if (!isset($_REQUEST['_url'])) {
            throw (new HTTP\Exception())->setCode(HTTP\Exception::CODE_INVALID_ROUTE);
        }

        $urlArray = explode('/', $_REQUEST['_url']);

        if (!isset($urlArray[1])) {
            throw (new HTTP\Exception())->setCode(HTTP\Exception::CODE_INVALID_ROUTE);
        }

        return $urlArray[1];
    }

    protected function addBuildableDirectories(array $httpBuildableDirectoryMap) : HTTPInterface
    {
        if (isset($httpBuildableDirectoryMap[self::YAML_KEY_BUILDABLE_DIRECTORIES])) {
            foreach ($httpBuildableDirectoryMap[self::YAML_KEY_BUILDABLE_DIRECTORIES] as $directory) {
                $this->getProteanContainerBuilder()
                    ->getDiscoverableDirectories()
                    ->addDirectoryPathFilter($directory);
            }
        }

        return $this;
    }

    protected function addWelcomeBaskets(array $httpBuildableDirectoryMap) : HTTPInterface
    {
        if (isset($httpBuildableDirectoryMap[self::YAML_KEY_WELCOME_BASKETS])) {
            foreach ($httpBuildableDirectoryMap[self::YAML_KEY_WELCOME_BASKETS] as $welcomeBasket) {
                $this->getProteanContainerBuilder()
                    ->getDiscoverableDirectories()
                    ->getWelcomeBaskets()
                    ->addWelcomeBasket($welcomeBasket);
            }
        }

        return $this;
    }

    protected function addAppendedPaths(array $httpBuildableDirectoryMap) : HTTPInterface
    {
        if (isset($httpBuildableDirectoryMap[self::YAML_KEY_APPENDED_PATHS])) {
            foreach ($httpBuildableDirectoryMap[self::YAML_KEY_APPENDED_PATHS] as $path) {
                $this->getProteanContainerBuilder()
                    ->getDiscoverableDirectories()
                    ->appendPath(
                        $this->getProteanContainerBuilder()->getFilesystemProperties()->getRootDirectoryPath() . '/' . $path
                    );
            }
        }

        return $this;
    }
}
