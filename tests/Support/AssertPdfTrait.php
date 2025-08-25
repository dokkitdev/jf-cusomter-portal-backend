<?php

namespace App\Tests\Support;

trait AssertPdfTrait
{
    protected function assertPdfEquals(string $expectedPdfContent, string $actualPdfContent): void
    {
        $expectedPdfContent = preg_replace('/\/CreationDate\(D:.*\'\)/','', $expectedPdfContent);
        $expectedPdfContent = preg_replace('/\/ID \[ <.*>\n<.*> \]/','', $expectedPdfContent);
        $expectedPdfContent = preg_replace('/\/DocChecksum \/.*\n>>/','', $expectedPdfContent);

        $actualPdfContent = preg_replace('/\/CreationDate\(D:.*\'\)/','', $actualPdfContent);
        $actualPdfContent = preg_replace('/\/ID \[ <.*>\n<.*> \]/','', $actualPdfContent);
        $actualPdfContent = preg_replace('/\/DocChecksum \/.*\n>>/','', $actualPdfContent);

        $this->assertEquals($expectedPdfContent, $actualPdfContent);
    }
}