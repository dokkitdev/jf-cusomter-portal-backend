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

    /**
     * @dataProvider  getTemplateTypes
     *
     * @param string $type
     */
    public function testAnnualPaymentTemplateDownload(string $type)
    {
        Storage::fake('templates');

        Storage::disk('templates')->put(config("defaults.private_contract.templates.names.{$type}"), 'test_content');

        $response = $this->actingAs($this->admin)->json('get', "/private-contracts/templates/{$type}/download");

        $response->assertStatus(Response::HTTP_OK);

        Storage::delete(config("defaults.private_contract.template_names.{$type}"));
    }

    /**
     * @dataProvider  getTemplateTypes
     *
     * @param string $type
     */
    public function testAnnualPaymentTemplateUpload(string $type)
    {
        Storage::fake('templates');

        $filename = config("defaults.private_contract.templates.names.{$type}");

        $response = $this->actingAs($this->admin)->json('put', "/private-contracts/templates/{$type}/upload", [
            'template' => UploadedFile::fake()->create(
                $filename,
                500,
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ),
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        Storage::delete(config("defaults.private_contract.template.names.{$type}"));
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

    public function testGenerateLettersForAnnualAndDirectDebitContractsProjectType()
    {
        $storageTemplates = Storage::fake('templates');
        $storageDocs = Storage::fake('private_contracts_docs');

        $storageTemplates->put(config("defaults.private_contract.templates.names.annual_payment"), $this->getFixture('annual-contracts.docx'));
        $storageTemplates->put(config("defaults.private_contract.templates.names.direct_debit"), $this->getFixture('direct-debit-contracts.docx'));

        $response = $this->actingAs($this->admin)->json('post', "/private-contracts/generate-letters", [
            'private_contract_ids' => [2, 3],
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $actualAnnualContent = $this->getPhpWordFileText($storageDocs->path('2.Annual.2018-11-11.102.docx'));
        $actualDirectContent = $this->getPhpWordFileText($storageDocs->path('1.Direct.2018-11-11.103.docx'));

        $expectedAnnualContent = $this->getPhpWordFileText($this->getFixturePath('/docs/2.Annual.2018-11-11.102.docx'));
        $expectedDirectContent = $this->getPhpWordFileText($this->getFixturePath('/docs/1.Direct.2018-11-11.103.docx'));

        $this->assertEquals($expectedAnnualContent, $actualAnnualContent);
        $this->assertEquals($expectedDirectContent, $actualDirectContent);

        $this->assertTrue($storageDocs->exists('2.Annual.2018-11-11.102.pdf'));
        $this->assertTrue($storageDocs->exists('1.Direct.2018-11-11.103.pdf'));
    }

    public function testGenerateLettersForAnnualAndDirectDebitContractsServiceType()
    {
        $storageTemplates = Storage::fake('templates');
        $storageDocs = Storage::fake('private_contracts_docs');

        $storageTemplates->put(config("defaults.private_contract.templates.names.annual_payment"), $this->getFixture('annual-contracts.docx'));
        $storageTemplates->put(config("defaults.private_contract.templates.names.direct_debit"), $this->getFixture('direct-debit-contracts.docx'));

        $response = $this->actingAs($this->admin)->json('post', "/private-contracts/generate-letters", [
            'private_contract_ids' => [4, 5],
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $actualAnnualContent = $this->getPhpWordFileText($storageDocs->path('2.Annual.2018-11-11.104.docx'));
        $actualDirectContent = $this->getPhpWordFileText($storageDocs->path('2.Direct.2018-11-11.105.docx'));

        $expectedAnnualContent = $this->getPhpWordFileText($this->getFixturePath('/docs/2.Annual.2018-11-11.104.docx'));
        $expectedDirectContent = $this->getPhpWordFileText($this->getFixturePath('/docs/2.Direct.2018-11-11.105.docx'));

        $this->assertEquals($expectedAnnualContent, $actualAnnualContent);
        $this->assertEquals($expectedDirectContent, $actualDirectContent);

        $this->assertTrue($storageDocs->exists('2.Annual.2018-11-11.104.pdf'));
        $this->assertTrue($storageDocs->exists('2.Direct.2018-11-11.105.docx'));
    }

    public function testGenerateLettersNotExists()
    {
        $response = $this->actingAs($this->admin)->json('post', "/private-contracts/generate-letters", [
            'private_contract_ids' => [0],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('message', 'The given data was invalid.');
    }

    public function testGenerateLettersNotAuth()
    {
        $response = $this->json('post', "/private-contracts/generate-letters", [
            'private_contract_ids' => [2],
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
