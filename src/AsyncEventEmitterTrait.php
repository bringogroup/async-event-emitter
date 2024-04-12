<?php

namespace Bringo\AsyncEventEmitter;

use Amp\Promise;
use Evenement\EventEmitterTrait;
use InvalidArgumentException;
use function Amp\asyncCall;
use function Amp\call;
use function Amp\Promise\any;

trait AsyncEventEmitterTrait
{
    use EventEmitterTrait;

    public function emit($event, array $arguments = [])
    {
        asyncCall(function () use ($event, $arguments) {
            yield $this->emitAsync($event, $arguments);
        });
    }

    public function emitAsync($event, array $arguments = []): Promise
    {
        return call(function () use ($event, $arguments) {
            if ($event === null) {
                throw new InvalidArgumentException('event name must not be null');
            }

            $promises = [];

            if (isset($this->listeners[$event])) {
                foreach ($this->listeners[$event] as $listener) {
                    $promises[] = call($listener, ...$arguments);
                }
            }

            if (isset($this->onceListeners[$event])) {
                $listeners = $this->onceListeners[$event];
                unset($this->onceListeners[$event]);
                foreach ($listeners as $listener) {
                    $promises[] = call($listener, ...$arguments);
                }
            }

            return any($promises);
        });
    }
}