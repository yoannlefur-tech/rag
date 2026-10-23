<?php

declare(strict_types=1);

namespace App\Rag;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class VoyageEmbeddingClient implements EmbeddingClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $http,
        #[Autowire('%env(VOYAGE_API_KEY)%')] private readonly string $apiKey,
    ) {
    }

    public function embed(array $texts, string $inputType = 'document'): array
    {
        $response = $this->http->request('POST', 'https://api.voyageai.com/v1/embeddings', [
            'auth_bearer' => $this->apiKey,
            'json' => ['model' => 'voyage-3', 'input' => $texts, 'input_type' => $inputType],
        ]);

        return array_map(
            static fn (array $item): array => $item['embedding'],
            $response->toArray()['data'],
        );
    }
}