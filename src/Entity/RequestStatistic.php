<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RequestStatisticRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequestStatisticRepository::class)]
#[ORM\Table(name: 'request_statistics')]
#[ORM\UniqueConstraint(name: 'uniq_request_signature', columns: ['signature'])]
class RequestStatistic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct(
        #[ORM\Column(length: 64)]
        private string $signature,
        #[ORM\Column]
        private int $int1,
        #[ORM\Column]
        private int $int2,
        #[ORM\Column(name: 'limit_value')]
        private int $limit,
        #[ORM\Column(length: 100)]
        private string $str1,
        #[ORM\Column(length: 100)]
        private string $str2,
        #[ORM\Column]
        private int $hits = 0,
    ) {}

    public function getSignature(): string
    {
        return $this->signature;
    }
    public function getInt1(): int
    {
        return $this->int1;
    }
    public function getInt2(): int
    {
        return $this->int2;
    }
    public function getLimit(): int
    {
        return $this->limit;
    }
    public function getStr1(): string
    {
        return $this->str1;
    }
    public function getStr2(): string
    {
        return $this->str2;
    }
    public function getHits(): int
    {
        return $this->hits;
    }
}
