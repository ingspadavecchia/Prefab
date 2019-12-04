<?php
declare(strict_types=1);

namespace Neighborhoods\Prefab\AnnotationProcessorRecord\StaticContextRecord\Factory;

use Neighborhoods\Prefab\AnnotationProcessorRecord\StaticContextRecord\FactoryInterface;

trait AwareTrait
{
    protected $AnnotationProcessorRecordStaticContextRecordFactory;

    public function setAnnotationProcessorRecordStaticContextRecordFactory(FactoryInterface $StaticContextRecordFactory): self
    {
        if ($this->hasActorFactory()) {
            throw new \LogicException('ActorFactory is already set.');
        }
        $this->AnnotationProcessorRecordStaticContextRecordFactory = $StaticContextRecordFactory;

        return $this;
    }

    protected function getAnnotationProcessorRecordStaticContextRecordFactory(): FactoryInterface
    {
        if (!$this->hasActorFactory()) {
            throw new \LogicException('ActorFactory is not set.');
        }

        return $this->AnnotationProcessorRecordStaticContextRecordFactory;
    }

    protected function hasActorFactory(): bool
    {
        return isset($this->AnnotationProcessorRecordStaticContextRecordFactory);
    }

    protected function unsetAnnotationProcessorRecordStaticContextRecordFactory(): self
    {
        if (!$this->hasActorFactory()) {
            throw new \LogicException('ActorFactory is not set.');
        }
        unset($this->AnnotationProcessorRecordStaticContextRecordFactory);

        return $this;
    }
}
