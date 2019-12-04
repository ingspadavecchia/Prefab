<?php
declare(strict_types=1);

namespace Neighborhoods\Prefab\AnnotationProcessorRecord\StaticContextRecord;

use Neighborhoods\Prefab\AnnotationProcessorRecord\StaticContextRecordInterface;

trait AwareTrait
{
    protected $AnnotationProcessorRecordStaticContextRecord;

    public function setAnnotationProcessorRecordStaticContextRecord(StaticContextRecordInterface $StaticContextRecord): self
    {
        if ($this->hasActor()) {
            throw new \LogicException('Actor is already set.');
        }
        $this->AnnotationProcessorRecordStaticContextRecord = $StaticContextRecord;

        return $this;
    }

    protected function getAnnotationProcessorRecordStaticContextRecord(): StaticContextRecordInterface
    {
        if (!$this->hasActor()) {
            throw new \LogicException('Actor is not set.');
        }

        return $this->AnnotationProcessorRecordStaticContextRecord;
    }

    protected function hasActor(): bool
    {
        return isset($this->AnnotationProcessorRecordStaticContextRecord);
    }

    protected function unsetAnnotationProcessorRecordStaticContextRecord(): self
    {
        if (!$this->hasActor()) {
            throw new \LogicException('Actor is not set.');
        }
        unset($this->AnnotationProcessorRecordStaticContextRecord);

        return $this;
    }
}
