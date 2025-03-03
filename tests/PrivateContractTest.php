<?php

namespace App\Tests;

use App\Models\PrivateContract;
use App\Models\User;
use App\Tests\Support\MockHttpRequestServiceTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PrivateContractTest extends TestCase
{
    use MockHttpRequestServiceTrait;

    protected $admin;

    protected array $requiredOriginStates = [
        'private_contracts',
        'private_contract_cost_centers',
        'private_contract_cost_center_items',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
    }

    public function testSyncPrivateContract()
    {
        $this->mockHttpRequestService($this->getJsonFixture('requests_chain.json'));

        $this->artisan('simpro:sync-recurring-invoices');

        $this->assertChangesEqualsFixture('private_contracts');
        $this->assertChangesEqualsFixture('private_contract_cost_centers');
        $this->assertChangesEqualsFixture('private_contract_cost_center_items');
    }

    public function getTemplateTypes(): array
    {
        return [
            [
                'type' => PrivateContract::TEMPLATE_TYPE_ANNUAL_PAYMENT,
            ],
            [
                'type' => PrivateContract::TEMPLATE_TYPE_DIRECT_DEBIT,
            ],
        ];
    }

    public function testAnnualPaymentTemplateDownload()
    {
        $content = 'test_content';

        Storage::fake('templates');

        Storage::disk('templates')->put('annual-contracts.docx', $content);

        $response = $this->actingAs($this->admin)->json('get', '/private-contracts/templates/annual_payment/download');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($content, $response->streamedContent());
    }

    public function testDirectDebitTemplateDownload()
    {
        $content = 'test_content';

        Storage::fake('templates');

        Storage::disk('templates')->put('direct-debit-contracts.docx', $content);

        $response = $this->actingAs($this->admin)->json('get', '/private-contracts/templates/direct_debit/download');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($content, $response->streamedContent());
    }

    public function testAnnualPaymentTemplateUpload()
    {
        $content = 'test_content';

        Storage::fake('templates');

        $response = $this->actingAs($this->admin)->json('put', "/private-contracts/templates/annual_payment/upload", [
            'template' => UploadedFile::fake()->create(
                'annual-contracts.docx',
                $content,
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',

            ),
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $uploadedFileContent = Storage::disk('templates')->get('annual-contracts.docx');

        $this->assertEquals($content, $uploadedFileContent);
    }

    public function testDirectDebitTemplateUpload()
    {
        Storage::fake('templates');

        $content = 'test_content';

        $response = $this->actingAs($this->admin)->json('put', "/private-contracts/templates/direct_debit/upload", [
            'template' => UploadedFile::fake()->create(
                'direct-debit-contracts.docx',
                $content,
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ),
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $uploadedFileContent = Storage::disk('templates')->get('direct-debit-contracts.docx');

        $this->assertEquals($content, $uploadedFileContent);
    }

    public function testDownloadNotExists()
    {
        $response = $this->actingAs($this->admin)->json('get', '/private-contracts/templates/not_exists/download');

        $response->assertUnprocessable();

        $response->assertJson([
            'error' => 'The selected not_exists is invalid.',
        ]);
    }

    public function testDownloadNoAuth()
    {
        $response = $this->json('get', '/private-contracts/templates/direct_debit/download');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUploadTypeNotExists()
    {
        $response = $this->actingAs($this->admin)->json('put', "/private-contracts/templates/not_exists/upload", [
            'template' => UploadedFile::fake()->create(
                'fakename',
                100,
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ),
        ]);

        $response->assertUnprocessable();

        $response->assertJson([
            'error' => 'The selected not_exists is invalid.',
        ]);
    }

    public function testUploadNoAuth()
    {
        $response = $this->json('put', 'private-contracts/templates/not_exists/upload');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
