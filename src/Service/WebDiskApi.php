<?php

declare(strict_types=1);

namespace BlueGrid\Service;

use BlueGrid\Contract\WebDiskApiInterface;
use BlueGrid\Exception\WebDiskHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @psalm-import-type RawFilesData from \BlueGrid\Contract\WebDiskApiInterface
 * @psalm-import-type Files from \BlueGrid\Contract\WebDiskApiInterface
 */
final readonly class WebDiskApi implements WebDiskApiInterface
{
    public function __construct(
        private HttpClientInterface $client,
        private string              $baseUrl,
    ) {
    }

    /**
     * @return Files
     */
    public function getAllPaths(): array
    {
        $data = $this->get('/test');

        return \array_map(
            static fn(array $item) => $item['fileUrl'],
            $data['items']
        );
    }

    /**
     * @return RawFilesData
     */
    private function get(string $endpoint): array
    {
        $response = $this->client->request(
            Request::METHOD_GET,
            \sprintf("%s%s", $this->baseUrl, $endpoint),
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]
        );

        $body = $response->getContent();

        if ($response->getStatusCode() > 400) {
            throw new WebDiskHttpException($response->getStatusCode(), $body);
        }

        return json_decode($body, true); // @phpstan-ignore-line
    }
}