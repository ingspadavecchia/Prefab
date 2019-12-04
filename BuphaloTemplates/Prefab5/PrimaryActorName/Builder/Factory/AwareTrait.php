<?php
declare(strict_types=1);

namespace Neighborhoods\BuphaloTemplateTree\PrimaryActorName\Builder\Factory;

use Neighborhoods\BuphaloTemplateTree\PrimaryActorName\Builder\FactoryInterface;

trait AwareTrait
{
    protected $PrimaryActorNameBuilderFactory;

    public function setPrimaryActorNameBuilderFactory(FactoryInterface $PrimaryActorNameBuilderFactory): self
    {
        if ($this->hasActorBuilderFactory()) {
            throw new \LogicException('ActorBuilderFactory is already set.');
        }
        $this->PrimaryActorNameBuilderFactory = $PrimaryActorNameBuilderFactory;

        return $this;
    }

    protected function getPrimaryActorNameBuilderFactory(): FactoryInterface
    {
        if (!$this->hasActorBuilderFactory()) {
            throw new \LogicException('ActorBuilderFactory is not set.');
        }

        return $this->PrimaryActorNameBuilderFactory;
    }

    protected function hasActorBuilderFactory(): bool
    {
        return isset($this->PrimaryActorNameBuilderFactory);
    }

    protected function unsetPrimaryActorNameBuilderFactory(): self
    {
        if (!$this->hasActorBuilderFactory()) {
            throw new \LogicException('ActorBuilderFactory is not set.');
        }
        unset($this->PrimaryActorNameBuilderFactory);

        return $this;
    }
}
