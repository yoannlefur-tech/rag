<?php

declare(strict_types=1);

namespace App\Rag;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

final class RetrievalService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly EmbeddingClientInterface $embeddings,
    ) {
    }

    /** @return list<array{title: string, position: int, content: string, distance: float}> */
    public function search(string $question, int $limit = 5): array
    {
        $vector = $this->embeddings->embed([$question], 'query')[0];

        return $this->connection->fetchAllAssociative(
            'SELECT d.title, c.position, c.content,
                    c.embedding <=> CAST(:q AS vector) AS distance
             FROM chunk c
             JOIN document d ON d.id = c.document_id
             ORDER BY c.embedding <=> CAST(:q AS vector)
             LIMIT :limit',
            ['q' => '[' . implode(',', $vector) . ']', 'limit' => $limit],
            ['limit' => ParameterType::INTEGER],
        );
    }
}