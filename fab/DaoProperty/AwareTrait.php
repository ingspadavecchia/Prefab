<?php
declare(strict_types=1);

namespace Neighborhoods\Prefab\DaoProperty;

use Neighborhoods\Prefab\DaoPropertyInterface;

trait AwareTrait
{
    protected $DaoProperty;

    public function setDaoProperty(DaoPropertyInterface $DaoProperty): self
    {
        if ($this->hasActor()) {
            throw new \LogicException('Actor is already set.');
        }
        $this->DaoProperty = $DaoProperty;

        return $this;
    }

    protected function getDaoProperty(): DaoPropertyInterface
    {
        if (!$this->hasActor()) {
            throw new \LogicException('Actor is not set.');
        }

        return $this->DaoProperty;
    }

    protected function hasActor(): bool
    {
        return isset($this->DaoProperty);
    }

    protected function unsetDaoProperty(): self
    {
        if (!$this->hasActor()) {
            throw new \LogicException('Actor is not set.');
        }
        unset($this->DaoProperty);

        return $this;
    }
}
