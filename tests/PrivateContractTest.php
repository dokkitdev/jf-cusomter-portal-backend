<?php

namespace App\Tests;

use App\Tests\Support\MockHttpRequestServiceTrait;

class PrivateContractTest extends TestCase
{
    use MockHttpRequestServiceTrait;

    protected array $requiredOriginStates = [
        'private_contracts',
        'private_contract_cost_centers'
    ];

    public function testSyncPrivateContract()
    {
        $this->mockHttpRequestService($this->getJsonFixture('requests_chain.json'));

        $this->artisan('simpro:sync-recurring-invoices');

        $this->assertChangesEqualsFixture('private_contracts');
        $this->assertChangesEqualsFixture('private_contract_cost_centers');
    }
}
