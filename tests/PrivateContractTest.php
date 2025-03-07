<?php

namespace App\Tests;

use App\Models\PrivateContract;
use App\Models\User;
use App\Tests\Support\MockHttpRequestServiceTrait;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PrivateContractTest extends TestCase
{
    use MockHttpRequestServiceTrait;

    protected $admin;
    protected FilesystemAdapter $docsStorage;
    protected FilesystemAdapter $templatesStorage;

    protected array $requiredOriginStates = [
        'private_contracts',
        'private_contract_cost_centers',
        'private_contract_cost_center_items',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->storageDocs = Storage::fake('private_contracts_docs');
        $this->storageTemplates = Storage::fake('templates');
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

        $this->storageTemplates->put('annual-contracts.docx', $content);

        $response = $this->actingAs($this->admin)->json('get', '/private-contracts/templates/annual_payment/download');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($content, $response->streamedContent());
    }

    public function testDirectDebitTemplateDownload()
    {
        $content = 'test_content';

        $this->storageTemplates->put('direct-debit-contracts.docx', $content);

        $response = $this->actingAs($this->admin)->json('get', '/private-contracts/templates/direct_debit/download');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($content, $response->streamedContent());
    }

    public function testAnnualPaymentTemplateUpload()
    {
        $content = 'test_content';

        $response = $this->actingAs($this->admin)->json('put', "/private-contracts/templates/annual_payment/upload", [
            'template' => UploadedFile::fake()->create(
                'annual-contracts.docx',
                $content,
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',

            ),
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $uploadedFileContent = $this->storageTemplates->get('annual-contracts.docx');

        $this->assertEquals($content, $uploadedFileContent);
    }

    public function testDirectDebitTemplateUpload()
    {
        $content = 'test_content';

        $response = $this->actingAs($this->admin)->json('put', "/private-contracts/templates/direct_debit/upload", [
            'template' => UploadedFile::fake()->create(
                'direct-debit-contracts.docx',
                $content,
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ),
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $uploadedFileContent = $this->storageTemplates->get('direct-debit-contracts.docx');

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

    public function testGenerateLettersForAnnualAndDirectDebitContractsProjectType()
    {
        $this->storageTemplates->put(config("defaults.private_contract.templates.names.annual_payment"), $this->getFixture('annual-contracts.docx'));
        $this->storageTemplates->put(config("defaults.private_contract.templates.names.direct_debit"), $this->getFixture('direct-debit-contracts.docx'));

        $response = $this->actingAs($this->admin)->json('post', "/private-contracts/generate-letters", [
            'private_contract_ids' => [2, 3],
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $actualAnnualContent = $this->getPhpWordFileText($this->storageDocs->path('2.Annual.2018-11-11.102.docx'));
        $actualDirectContent = $this->getPhpWordFileText($this->storageDocs->path('1.Direct.2018-11-11.103.docx'));

        $expectedAnnualContent = $this->getPhpWordFileText($this->getFixturePath('/docs/2.Annual.2018-11-11.102.docx'));
        $expectedDirectContent = $this->getPhpWordFileText($this->getFixturePath('/docs/1.Direct.2018-11-11.103.docx'));

        $this->assertEquals($expectedAnnualContent, $actualAnnualContent);
        $this->assertEquals($expectedDirectContent, $actualDirectContent);

        $this->assertTrue($this->storageDocs->exists('2.Annual.2018-11-11.102.pdf'));
        $this->assertTrue($this->storageDocs->exists('1.Direct.2018-11-11.103.pdf'));
    }

    public function testGenerateLettersForAnnualAndDirectDebitContractsServiceType()
    {
        $this->storageTemplates->put(config("defaults.private_contract.templates.names.annual_payment"), $this->getFixture('annual-contracts.docx'));
        $this->storageTemplates->put(config("defaults.private_contract.templates.names.direct_debit"), $this->getFixture('direct-debit-contracts.docx'));

        $response = $this->actingAs($this->admin)->json('post', "/private-contracts/generate-letters", [
            'private_contract_ids' => [4, 5],
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $actualAnnualContent = $this->getPhpWordFileText($this->storageDocs->path('2.Annual.2018-11-11.104.docx'));
        $actualDirectContent = $this->getPhpWordFileText($this->storageDocs->path('2.Direct.2018-11-11.105.docx'));

        $expectedAnnualContent = $this->getPhpWordFileText($this->getFixturePath('/docs/2.Annual.2018-11-11.104.docx'));
        $expectedDirectContent = $this->getPhpWordFileText($this->getFixturePath('/docs/2.Direct.2018-11-11.105.docx'));

        $this->assertEquals($expectedAnnualContent, $actualAnnualContent);
        $this->assertEquals($expectedDirectContent, $actualDirectContent);

        $this->assertTrue($this->storageDocs->exists('2.Annual.2018-11-11.104.pdf'));
        $this->assertTrue($this->storageDocs->exists('2.Direct.2018-11-11.105.docx'));
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

    public function testDownloadDocxDoc()
    {
        $filename = '2.Annual.2018-11-11.102.docx';

        $this->storageDocs->put($filename, $this->getFixture('/docs/2.Annual.2018-11-11.102.docx'));

        $response = $this->actingAs($this->admin)->json('get', '/private-contracts/download-doc', [
            'filename' => $filename,
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($this->getFixture('/docs/2.Annual.2018-11-11.102.docx'), $response->streamedContent());
    }

    public function testDownloadPdfDoc()
    {
        $filename = '2.Annual.2018-11-11.102.pdf';

        $this->storageDocs->put($filename, $this->getFixture('/docs/2.Annual.2018-11-11.102.pdf'));

        $response = $this->actingAs($this->admin)->json('get', '/private-contracts/download-doc', [
            'filename' => $filename,
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($this->getFixture('/docs/2.Annual.2018-11-11.102.pdf'), $response->streamedContent());
    }

    public function testDownloadDocNotExists()
    {
        $response = $this->actingAs($this->admin)->json('get', '/private-contracts/download-doc', [
            'filename' => '2.Annual.2018-11-11.102.docx',
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDownloadDocNoAuth()
    {
        $response = $this->json('get', '/private-contracts/download-doc', [
            'filename' => '2.Annual.2018-11-11.102.docx',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function getSearchFilters(): array
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_by_all_user.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_user.json'
            ],
        ];
    }

    /**
     * @dataProvider  getSearchFilters
     *
     * @param array $filter
     * @param string $fixture
     */
    public function testSearch(array $filter, string $fixture)
    {
        $response = $this->actingAs($this->admin)->json('get', '/private-contracts', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture($fixture, $response->json());
    }
}
