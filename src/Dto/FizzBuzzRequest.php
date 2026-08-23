<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class FizzBuzzRequest
{
    public function __construct(
        #[Assert\Positive]
        public int $int1,
        #[Assert\Positive]
        public int $int2,
        #[Assert\Positive]
        #[Assert\LessThanOrEqual(100000)]
        public int $limit,
        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        public string $str1,
        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        public string $str2,
    ) {}
}
