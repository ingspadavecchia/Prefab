<?php
declare(strict_types=1);

namespace Neighborhoods\BuphaloTemplateTree\PrimaryActorName\Map\Repository;

use Neighborhoods\BuphaloTemplateTree\PrimaryActorName\Map\RepositoryInterface;

trait AwareTrait
{
    protected $PrimaryActorNameMapRepository;

    public function setPrimaryActorNameMapRepository(RepositoryInterface $PrimaryActorNameMapRepository): self
    {
        if ($this->hasActorMapRepository()) {
            throw new \LogicException('ActorMapRepository is already set.');
        }
        $this->PrimaryActorNameMapRepository = $PrimaryActorNameMapRepository;

        return $this;
    }

    protected function getPrimaryActorNameMapRepository(): RepositoryInterface
    {
        if (!$this->hasActorMapRepository()) {
            throw new \LogicException('ActorMapRepository is not set.');
        }

        return $this->PrimaryActorNameMapRepository;
    }

    protected function hasActorMapRepository(): bool
    {
        return isset($this->PrimaryActorNameMapRepository);
    }

    protected function unsetPrimaryActorNameMapRepository(): self
    {
        if (!$this->hasActorMapRepository()) {
            throw new \LogicException('ActorMapRepository is not set.');
        }
        unset($this->PrimaryActorNameMapRepository);

        return $this;
    }
}
