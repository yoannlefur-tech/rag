<?php

declare(strict_types=1);

namespace App\Ingestion;

final class Chunker
{
    public function __construct(
        private readonly int $size = 400,     // ~500 tokens
        private readonly int $overlap = 60,   // ~15 %
    ) {
    }

    /** @return list<string> */
    public function split(string $text): array
    {
        $chunks = [];
        $length = mb_strlen($text);
        $step = $this->size - $this->overlap;

        for ($start = 0; $start < $length; $start += $step) {
            $chunk = trim(mb_substr($text, $start, $this->size));
            if ($chunk !== '') {
                $chunks[] = $chunk;
            }
            if ($start + $this->size >= $length) {
                break;
            }
        }

        return $chunks;
    }
}