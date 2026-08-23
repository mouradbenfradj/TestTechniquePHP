<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\MostFrequentRequestProvider;

#[ApiResource(operations: [new Get(uriTemplate: '/statistics/most-frequent', provider: MostFrequentRequestProvider::class)])]
final readonly class MostFrequentRequest
{
    public function __construct(
        public int $int1,
        public int $int2,
        public int $limit,
        public string $str1,
        public string $str2,
        public int $hits,
    ) {}
}
