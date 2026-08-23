<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\MostFrequentRequest;
use App\Repository\RequestStatisticRepository;

final class MostFrequentRequestProvider implements ProviderInterface
{
    public function __construct(private RequestStatisticRepository $statistics) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?MostFrequentRequest
    {
        $statistic = $this->statistics->mostFrequent();
        if (null === $statistic) {
            return null;
        }

        return new MostFrequentRequest(
            $statistic->getInt1(),
            $statistic->getInt2(),
            $statistic->getLimit(),
            $statistic->getStr1(),
            $statistic->getStr2(),
            $statistic->getHits(),
        );
    }
}
