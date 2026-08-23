<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Dto\FizzBuzzRequest;
use App\Service\FizzBuzzGenerator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FizzBuzzGeneratorTest extends TestCase
{
    /** @param list<string> $expected */
    #[DataProvider('generationCases')]
    public function testGeneratesExpectedSequence(int $int1, int $int2, int $limit, string $str1, string $str2, array $expected): void
    {
        $result = (new FizzBuzzGenerator())->generate(new FizzBuzzRequest($int1, $int2, $limit, $str1, $str2));

        self::assertSame($expected, $result);
    }

    /** @return iterable<string, array{int, int, int, string, string, list<string>}> */
    public static function generationCases(): iterable
    {
        yield 'classic fizzbuzz' => [3, 5, 15, 'fizz', 'buzz', ['1', '2', 'fizz', '4', 'buzz', 'fizz', '7', '8', 'fizz', 'buzz', '11', 'fizz', '13', '14', 'fizzbuzz']];
        yield 'custom words' => [2, 3, 6, 'pair', 'triple', ['1', 'pair', 'triple', 'pair', '5', 'pairtriple']];
        yield 'same divisors concatenate twice' => [2, 2, 4, 'A', 'B', ['1', 'AB', '3', 'AB']];
        yield 'divisor greater than limit' => [10, 20, 3, 'ten', 'twenty', ['1', '2', '3']];
        yield 'limit one' => [3, 5, 1, 'fizz', 'buzz', ['1']];
    }
}
