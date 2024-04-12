<?php

use Amp\Deferred;
use Amp\Loop;
use Amp\Success;
use Bringo\AsyncEventEmitter\AsyncEventEmitterInterface;
use Bringo\AsyncEventEmitter\AsyncEventEmitterTrait;

require_once __DIR__ . '/../vendor/autoload.php';

class A implements AsyncEventEmitterInterface
{
    use AsyncEventEmitterTrait;
}

Loop::run(function () {
    $a = new A();
    $a->on('example_event_name', function ($arg1, $arg2, $arg3) {
//        yield new \Amp\Success();

        $deferred = new Deferred();

        Loop::delay(1000, function() use($deferred, $arg1, $arg2, $arg3){
            var_dump('Old');
            var_dump($arg1, $arg2, $arg3);
            $deferred->resolve();
        });

        return $deferred->promise();
    });
    $a->on('example_event_name', function ($arg1, $arg2, $arg3) {
        yield new Success();

        var_dump('New');
        var_dump($arg1, $arg2, $arg3);
    });

    yield $a->emitAsync('example_event_name', ['arg1', 'arg2', 'arg3']);
    var_dump('finish');
});