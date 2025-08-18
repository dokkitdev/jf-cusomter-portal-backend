<?php

namespace App\Tests;

use App\Tests\Support\AssertStorageTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class LetterTemplateTest extends TestCase
{
    use AssertStorageTrait;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('letter_templates');
    }

    /*************************************
     *            GET GROUPED            *
     *************************************/

    public function getDataTestGetGrouped(): array
    {
        return [
            [
                'userId' => 1,
                'statusCode' => Response::HTTP_OK,
                'responseFixture' => 'get_grouped__success.json',
            ],
            [
                'userId' => 2,
                'statusCode' => Response::HTTP_FORBIDDEN,
                'responseFixture' => 'get_grouped__not_admin.json',
            ],
            [
                'userId' => null,
                'statusCode' => Response::HTTP_UNAUTHORIZED,
                'responseFixture' => 'get_grouped__no_auth.json',
            ],
        ];
    }

    /**
     * @dataProvider getDataTestGetGrouped
     */
    public function testGetGrouped(?int $userId, int $statusCode, string $responseFixture): void
    {
        if (isset($userId)) {
            $this->actingAsById($userId);
        }

        $response = $this->json('get', '/letter-templates');

        $response->assertStatus($statusCode);

        $this->assertEqualsFixture($responseFixture, $response->json());
    }

    /*************************************
     *               UPLOAD              *
     *************************************/

    public function getDataTestUpload(): array
    {
        return [
            [
                'userId' => 1,
                'templateName' => 'private__annual_contracts',
                'initialStorageState' => null,
                'statusCode' => Response::HTTP_NO_CONTENT,
                'responseFixture' => null,
                'expectedStorageState' => 'upload__success__storage_state',
            ],
            [
                'userId' => 2,
                'templateName' => 'private__annual_contracts',
                'initialStorageState' => null,
                'statusCode' => Response::HTTP_FORBIDDEN,
                'responseFixture' => 'upload__not_admin__response.json',
                'expectedStorageState' => null,
            ],
            [
                'userId' => 1,
                'templateName' => 'unknown_template_name',
                'initialStorageState' => null,
                'statusCode' => Response::HTTP_NOT_FOUND,
                'responseFixture' => 'upload__unknown_template_name__response.json',
                'expectedStorageState' => null,
            ],
            [
                'userId' => 1,
                'templateName' => 'private__annual_contracts',
                'initialStorageState' => 'upload__update_template__initial_storage_state',
                'statusCode' => Response::HTTP_NO_CONTENT,
                'responseFixture' => null,
                'expectedStorageState' => 'upload__update_template__storage_state',
            ],
            [
                'userId' => null,
                'templateName' => 'private__annual_contracts',
                'initialStorageState' => null,
                'statusCode' => Response::HTTP_UNAUTHORIZED,
                'responseFixture' => 'upload__no_auth__response.json',
                'expectedStorageState' => null,
            ],
        ];
    }

    /**
     * @dataProvider getDataTestUpload
     */
    public function testUpload(
        ?int $userId,
        string $templateName,
        ?string $initialStorageState,
        int $statusCode,
        ?string $responseFixture,
        ?string $expectedStorageState
    ): void {
        $file = new UploadedFile($this->getFixturePath('template-example.docx'), 'file_name', null, null, true);

        if (isset($initialStorageState)) {
            $this->prepareInitialStorageState($initialStorageState, 'letter_templates');
        }

        if (isset($userId)) {
            $this->actingAsById($userId);
        }

        $response = $this->json('post', "/letter-templates/{$templateName}/upload", [
            'file' => $file,
        ]);

        if ($statusCode === Response::HTTP_NO_CONTENT) {
            $response->assertNoContent();
        } else {
            $response->assertStatus($statusCode);
            $this->assertEqualsFixture($responseFixture, $response->json());
        }

        if (isset($expectedStorageState)) {
            $this->assertDirectoryEquals(
                $this->getFixturePath($expectedStorageState),
                Storage::disk('letter_templates')->path(''),
            );
        } else {
            $this->assertStorageEmpty('letter_templates');
        }
    }

    /*************************************
     *               UPLOAD              *
     *************************************/

    public function getDataTestDownload(): array
    {
        return [
            [
                'userId' => 1,
                'templateName' => 'private__annual_contracts',
                'initialStorageState' => 'download__success__initial_storage_state',
                'statusCode' => Response::HTTP_OK,
                'responseFixture' => null,
                'expectedFileName' => 'private__annual_contracts.docx',
                'expectedFileContentFixture' => 'template-example.docx',
            ],
            [
                'userId' => 2,
                'templateName' => 'private__annual_contracts',
                'initialStorageState' => 'download__not_admin__initial_storage_state',
                'statusCode' => Response::HTTP_FORBIDDEN,
                'responseFixture' => 'download__not_admin__response.json',
                'expectedFileName' => null,
                'expectedFileContentFixture' => null,
            ],
            [
                'userId' => 1,
                'templateName' => 'unknown_template_name',
                'initialStorageState' => 'download__unknown_template_name__initial_storage_state',
                'statusCode' => Response::HTTP_NOT_FOUND,
                'responseFixture' => 'download__unknown_template_name__response.json',
                'expectedFileName' => null,
                'expectedFileContentFixture' => null,
            ],
            [
                'userId' => 1,
                'templateName' => 'private__annual_contracts',
                'initialStorageState' => 'download__file_not_uploaded__initial_storage_state',
                'statusCode' => Response::HTTP_NOT_FOUND,
                'responseFixture' => 'download__file_not_uploaded__response.json',
                'expectedFileName' => null,
                'expectedFileContentFixture' => null,
            ],
            [
                'userId' => null,
                'templateName' => 'private__annual_contracts',
                'initialStorageState' => null,
                'statusCode' => Response::HTTP_UNAUTHORIZED,
                'responseFixture' => 'download__no_auth__response.json',
                'expectedFileName' => null,
                'expectedFileContentFixture' => null,
            ],
        ];
    }

    /**
     * @dataProvider getDataTestDownload
     */
    public function testDownload(
        ?int $userId,
        string $templateName,
        ?string $initialStorageState,
        int $statusCode,
        ?string $responseFixture,
        ?string $expectedFileName,
        ?string $expectedFileContentFixture
    ): void {
        if (isset($initialStorageState)) {
            $this->prepareInitialStorageState($initialStorageState, 'letter_templates');
        }

        if (isset($userId)) {
            $this->actingAsById($userId);
        }

        $response = $this->json('post', "/letter-templates/{$templateName}/download");

        $response->assertStatus($statusCode);

        if (isset($responseFixture)) {
            $this->assertEqualsFixture($responseFixture, $response->json());
        } else {
            $response->assertHeader('Content-Disposition', "attachment; filename=\"{$expectedFileName}\"");
            $this->assertEquals($this->getFixture($expectedFileContentFixture), $response->getContent());
        }
    }
}
