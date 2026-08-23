<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\FizzBuzzRequest;
use App\Repository\RequestStatisticRepository;
use App\Service\FizzBuzzGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class FizzBuzzProvider implements ProviderInterface
{
    public function __construct(
        private FizzBuzzGenerator $generator,
        private RequestStatisticRepository $statistics,
        private ValidatorInterface $validator,
    ) {}

    /** @return list<string> */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $request = $context['request'] ?? null;
        if (!$request instanceof Request) {
            throw new BadRequestHttpException('A HTTP request is required.');
        }

        $parameters = $this->parameters($request);
        $violations = $this->validator->validate($parameters);
        if (count($violations) > 0) {
            throw new BadRequestHttpException((string) $violations);
        }

        $signature = hash('sha256', json_encode([
            $parameters->int1,
            $parameters->int2,
            $parameters->limit,
            $parameters->str1,
            $parameters->str2,
        ], JSON_THROW_ON_ERROR));
        $this->statistics->record($signature, $parameters->int1, $parameters->int2, $parameters->limit, $parameters->str1, $parameters->str2);

        return $this->generator->generate($parameters);
    }

    private function parameters(Request $request): FizzBuzzRequest
    {
        $query = $request->query;
        $missingParameters = array_values(array_filter(
            ['int1', 'int2', 'limit', 'str1', 'str2'],
            static fn(string $name): bool => !$query->has($name) || '' === trim((string) $query->get($name)),
        ));
        if ([] !== $missingParameters) {
            throw new BadRequestHttpException(sprintf(
                'Veuillez renseigner les paramètres suivants : %s.',
                implode(', ', $missingParameters),
            ));
        }

        $int1 = $this->integer($query->get('int1'));
        $int2 = $this->integer($query->get('int2'));
        $limit = $this->integer($query->get('limit'));
        $str1 = $query->get('str1');
        $str2 = $query->get('str2');

        return new FizzBuzzRequest($int1, $int2, $limit, $str1, $str2);
    }

    private function integer(mixed $value): int
    {
        if (!is_string($value) || filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new BadRequestHttpException('int1, int2 and limit must be integers.');
        }

        return (int) $value;
    }
}
