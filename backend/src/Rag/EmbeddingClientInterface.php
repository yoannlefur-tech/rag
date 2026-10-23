<?php

declare(strict_types=1);

namespace App\Rag;

interface EmbeddingClientInterface
{
    /**
     * @param list<string> $texts
     * @param string $inputType
     * @return list<list<float>>
     */
    public function embed(array $texts, string $inputType = 'document'): array;
}