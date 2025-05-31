<?php

declare(strict_types=1);

namespace App\Sync;

use Amp\Sync\LocalMutex;
use Amp\Sync\Lock;
use Amp\Sync\Mutex;

final class ReentrantMutex implements Mutex
{
    private Mutex $mutex;
    private int $lockCount = 0;
    private ?int $owner = null;

    private ?Lock $currentLock = null;

    public function __construct()
    {
        $this->mutex = new LocalMutex();
    }

    #[\Override]
    public function acquire(): Lock
    {
        $currentFiber = \Fiber::getCurrent();
        $currentFiberId = $currentFiber ? spl_object_id($currentFiber) : -1;

        $releaseFn = function (): void {
            $this->releaseInternal();
        };

        if ($currentFiberId === $this->owner) {
            ++$this->lockCount;

            return new Lock($releaseFn);
        }

        $this->currentLock = $this->mutex->acquire();
        $this->owner = $currentFiberId;
        $this->lockCount = 1;

        return new Lock($releaseFn);
    }

    private function releaseInternal(): void
    {
        --$this->lockCount;
        if ($this->lockCount === 0) {
            $this->owner = null;
            $this->currentLock?->release();
        }
    }
}
