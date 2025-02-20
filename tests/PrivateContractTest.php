<?php

namespace App\Tests;

use App\Models\User;
use App\Tests\Support\SimproTestTrait;

class PrivateContractTest extends TestCase
{
    use SimproTestTrait;

    protected $admin;
    protected $user;
    protected $customer;

    protected array $requiredOriginStates = [
        'private_contracts',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
        $this->customer = User::find(3);
    }

    public function testSyncPrivateContract()
    {
        $this->mockSyncRecurringInvoicesCommand();

        $this->artisan('simpro:sync-recurring-invoices');

        $this->assertChangesEqualsFixture('private_contracts');
    }
}
