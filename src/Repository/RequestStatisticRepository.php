<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RequestStatistic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class RequestStatisticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestStatistic::class);
    }

    public function record(string $signature, int $int1, int $int2, int $limit, string $str1, string $str2): void
    {
        $this->getEntityManager()->getConnection()->executeStatement(
            'INSERT INTO request_statistics (signature, int1, int2, limit_value, str1, str2, hits)
             VALUES (:signature, :int1, :int2, :limit_value, :str1, :str2, 1)
             ON CONFLICT (signature) DO UPDATE SET hits = request_statistics.hits + 1',
            [
                'signature' => $signature,
                'int1' => $int1,
                'int2' => $int2,
                'limit_value' => $limit,
                'str1' => $str1,
                'str2' => $str2,
            ],
        );
    }

    public function mostFrequent(): ?RequestStatistic
    {
        return $this->createQueryBuilder('statistic')
            ->orderBy('statistic.hits', 'DESC')
            ->addOrderBy('statistic.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
