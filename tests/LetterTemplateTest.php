<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;

class LetterTemplateTest extends TestCase
{
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
}
