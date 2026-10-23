<?php

declare(strict_types=1);

namespace App\Ingestion;

use App\Entity\Document;
use App\Rag\EmbeddingClientInterface;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

final class IngestionService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Connection $connection,
        private readonly Chunker $chunker,
        private readonly EmbeddingClientInterface $embeddings,
    ) {
    }

    public function ingestMarkdown(string $title, string $content): int
    {
        $parts = $this->chunker->split($content);

        // 1. Embeddings d'abord, hors transaction (appels réseau)
        $vectors = [];
        foreach (array_chunk($parts, 128) as $batch) {
            array_push($vectors, ...$this->embeddings->embed($batch));
        }

        // 2. Écriture atomique
        return $this->em->wrapInTransaction(function () use ($title, $parts, $vectors): int {
            $document = new Document($title);
            $this->em->persist($document);
            $this->em->flush();

            foreach ($parts as $position => $text) {
                $this->connection->executeStatement(
                    'INSERT INTO chunk (document_id, position, content, embedding)
                     VALUES (:doc, :pos, :content, CAST(:vec AS vector))',
                    [
                        'doc' => $document->getId(),
                        'pos' => $position,
                        'content' => $text,
                        'vec' => '[' . implode(',', $vectors[$position]) . ']',
                    ],
                );
            }

            return $document->getId();
        });
    }
}