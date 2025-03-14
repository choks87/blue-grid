<?php

declare(strict_types=1);

namespace BlueGrid\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

abstract class WebTestCase extends KernelTestCase
{
    private const SERVER_DSN = 'https://localhost:9501';

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        parent::setUp();
    }

    protected function makeApiRequest(
        string            $method,
        string            $endpoint,
        string|array|null $body = null,
        array             $files = [],
        string            $token = null,
    ): void {
        $server = [
            'CONTENT_TYPE' => 'application/json',
        ];

        if (null !== $token) {
            $server['HTTP_AUTHORIZATION'] = sprintf('Bearer %s', $token);
        }

        if (null !== $body) {
            $body = \json_encode($body, JSON_THROW_ON_ERROR);
        }

        $this->client->request(
            $method,
            \sprintf("%s/api/%s", \trim(self::SERVER_DSN, '/'), \trim($endpoint, '/')),
            [],
            $files,
            $server,
            $body
        );
    }

    public function getResponseContent(): ?array
    {
        $content = $this->client->getResponse()->getContent();

        try {
            return \json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new \RuntimeException(\sprintf("Cannot parse JSON from API response content %s", $content));
        }
    }
}
