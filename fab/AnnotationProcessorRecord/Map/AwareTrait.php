<?php
declare(strict_types=1);

namespace Neighborhoods\Prefab\AnnotationProcessorRecord\Map;

use Neighborhoods\Prefab\AnnotationProcessorRecord\MapInterface;

trait AwareTrait
{
    protected $AnnotationProcessorRecords;

    public function setAnnotationProcessorRecordMap(MapInterface $AnnotationProcessorRecords): self
    {
        if ($this->hasActorMap()) {
            throw new \LogicException('Actors is already set.');
        }
        $this->AnnotationProcessorRecords = $AnnotationProcessorRecords;

        return $this;
    }

    protected function getAnnotationProcessorRecordMap(): MapInterface
    {
        if (!$this->hasActorMap()) {
            throw new \LogicException('Actors is not set.');
        }

        return $this->AnnotationProcessorRecords;
    }

    protected function hasActorMap(): bool
    {
        return isset($this->AnnotationProcessorRecords);
    }

    protected function unsetAnnotationProcessorRecordMap(): self
    {
        if (!$this->hasActorMap()) {
            throw new \LogicException('Actors is not set.');
        }
        unset($this->AnnotationProcessorRecords);

        return $this;
    }
}
