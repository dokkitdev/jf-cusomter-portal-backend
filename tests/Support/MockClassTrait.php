<?php

namespace App\Tests\Support;

use App\Services\HttpRequestService;
use Exception;
use Illuminate\Support\Arr;

trait MockClassTrait
{
    protected function mockClass(string $className, array $callChain = []): void
    {
        $methods = array_unique(Arr::pluck($callChain, 'method'));

        $mock = $this
            ->getMockBuilder($className)
            ->onlyMethods($methods)
            ->getMock();

        foreach ($callChain as $index => $methodCall) {
            $methodCallMock = $mock
                ->expects($this->at($index))
                ->method($methodCall['method'])
                ->with(...$methodCall['arguments']);

            if (Arr::has($methodCall, 'exception')) {
                $methodCallMock->willThrowException(new Exception('Test exception'));
            } elseif (Arr::has($methodCall, 'result')) {
                $methodCallMock->willReturn($methodCall['result']);
            }
        }

        $this->app->instance($className, $mock);
    }
}