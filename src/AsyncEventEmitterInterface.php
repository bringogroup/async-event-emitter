<?php

namespace Bringo\AsyncEventEmitter;

use Amp\Promise;
use Evenement\EventEmitterInterface;

interface AsyncEventEmitterInterface extends EventEmitterInterface
{
    public function emitAsync($event, array $arguments = []): Promise;
}