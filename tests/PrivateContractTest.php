<?php

namespace App\Tests;

use App\Tests\Support\MockHttpRequestServiceTrait;

class PrivateContractTest extends TestCase
{
    use MockHttpRequestServiceTrait;

    protected array $requiredOriginStates = [
        'private_contracts',
    ];

    public function testSyncPrivateContract()
    {
        $this->mockHttpRequestService($this->getJsonFixture('requests_chain.json'));

        $this->artisan('simpro:sync-recurring-invoices');

        $this->assertChangesEqualsFixture('private_contracts');
    }
}
