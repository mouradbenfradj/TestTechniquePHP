<?php

declare(strict_types=1);

namespace App\Tests\Dto;

use App\Dto\FizzBuzzRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use PHPUnit\Framework\TestCase;

final class FizzBuzzRequestTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    #[DataProvider('invalidRequestCases')]
    public function testRejectsInvalidRequest(FizzBuzzRequest $request, int $violationCount): void
    {
        self::assertCount($violationCount, $this->validator->validate($request));
    }

    /** @return iterable<string, array{FizzBuzzRequest, int}> */
    public static function invalidRequestCases(): iterable
    {
        yield 'zero first divisor' => [new FizzBuzzRequest(0, 5, 10, 'fizz', 'buzz'), 1];
        yield 'negative second divisor' => [new FizzBuzzRequest(3, -5, 10, 'fizz', 'buzz'), 1];
        yield 'zero limit' => [new FizzBuzzRequest(3, 5, 0, 'fizz', 'buzz'), 1];
        yield 'limit above maximum' => [new FizzBuzzRequest(3, 5, 100001, 'fizz', 'buzz'), 1];
        yield 'blank first word' => [new FizzBuzzRequest(3, 5, 10, '', 'buzz'), 1];
        yield 'blank second word' => [new FizzBuzzRequest(3, 5, 10, 'fizz', ''), 1];
        yield 'word too long' => [new FizzBuzzRequest(3, 5, 10, str_repeat('x', 101), 'buzz'), 1];
    }

    public function testAcceptsBoundaryValues(): void
    {
        $request = new FizzBuzzRequest(1, 100000, 100000, 'x', 'y');

        self::assertCount(0, $this->validator->validate($request));
    }
}
