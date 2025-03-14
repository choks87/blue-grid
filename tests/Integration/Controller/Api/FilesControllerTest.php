<?php

declare(strict_types=1);

namespace BlueGrid\Tests\Integration\Controller\Api;

use BlueGrid\Tests\Integration\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class FilesControllerTest extends WebTestCase
{
    public function testIndexMethod(): void
    {
        $this->makeApiRequest(Request::METHOD_GET, '/files');

        $content = $this->getResponseContent();

        $expected = [
            'data'     => [
                '192.168.0.2' => [
                    'waldoo.txt',
                ],
                '192.168.0.1' => [
                    'waldoo.txt',
                ],
            ],
            'page'     => 1,
            'per_page' => 100,
            'more'     => false,
        ];

        self::assertEquals($expected, $content);
    }
}
