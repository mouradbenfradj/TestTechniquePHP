<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\State\FizzBuzzProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/fizzbuzz',
            provider: FizzBuzzProvider::class,
            paginationEnabled: false,
            parameters: [
                'int1' => new QueryParameter(description: 'Obligatoire : premier diviseur positif', required: false, schema: ['type' => 'integer', 'example' => 3]),
                'int2' => new QueryParameter(description: 'Obligatoire : second diviseur positif', required: false, schema: ['type' => 'integer', 'example' => 5]),
                'limit' => new QueryParameter(description: 'Obligatoire : nombre maximum a generer', required: false, schema: ['type' => 'integer', 'maximum' => 100000, 'example' => 15]),
                'str1' => new QueryParameter(description: 'Obligatoire : remplacement des multiples de int1', required: false, schema: ['type' => 'string', 'example' => 'fizz']),
                'str2' => new QueryParameter(description: 'Obligatoire : remplacement des multiples de int2', required: false, schema: ['type' => 'string', 'example' => 'buzz']),
            ],
        ),
    ],
)]
final readonly class FizzBuzzResult
{
    public function __construct(
        public string $value,
    ) {}
}
