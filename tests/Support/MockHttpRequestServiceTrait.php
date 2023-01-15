<?php

namespace App\Tests\Support;

use App\Services\HttpRequestService;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Arr;

trait MockHttpRequestServiceTrait
{
    protected function mockHttpRequestService(array $requestsChain = [], bool $exportMode = false): void
    {
        $methods = array_unique(Arr::pluck($requestsChain, 'request.type'));

        $methods[] = 'getResponse';

        $mock = $this
            ->getMockBuilder(HttpRequestService::class)
            ->onlyMethods($methods)
            ->getMock();

        foreach ($requestsChain as $index => $call) {
            $requestData = $this->makeHttpRequestData($call['request'], $exportMode);
            $response = $this->makeHttpResponseData($call['response']);

            $mock
                ->expects($this->at($index * 2))
                ->method($call['request']['type'])
                ->with($call['request']['url'], $requestData, $call['request']['headers'])
                ->willReturnCallback(function () use ($mock) {
                    return $mock;
                });

            $mock
                ->expects($this->at(($index * 2) + 1))
                ->method('getResponse')
                ->willReturn($response);
        }

        $this->app->instance(HttpRequestService::class, $mock);
    }

    protected function makeHttpRequestData(array $request, bool $exportMode)
    {
        if (is_string($request['data']) && file_exists($this->getFixturePath($request['data']))) {
            if ($exportMode || $this->forceExportMode) {
                return $this->callback(function ($actualData) use ($request) {
                    if (is_array($actualData)) {
                        $this->exportJson($request['data'], $actualData);
                    } else {
                        $this->exportContent($actualData, $request['data']);
                    }

                    return true;
                });
            } else {
                return $this->getFixture($request['data']);
            }
        }

        return $request['data'];
    }

    protected function makeHttpResponseData(array $response): GuzzleResponse
    {
        if (is_array($response['data'])) {
            $response['data'] = json_encode($response['data']);
        } elseif (!empty($response['data']) && file_exists($this->getFixturePath($response['data']))) {
            $response['data'] = $this->getFixture($response['data']);
        }

        return new GuzzleResponse(
            $response['status_code'],
            $response['headers'],
            $response['data']
        );
    }
}