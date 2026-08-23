<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260822000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Stores request hit counts for the most frequent FizzBuzz request.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE request_statistics (id SERIAL NOT NULL, signature VARCHAR(64) NOT NULL, int1 INT NOT NULL, int2 INT NOT NULL, limit_value INT NOT NULL, str1 VARCHAR(100) NOT NULL, str2 VARCHAR(100) NOT NULL, hits INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_request_signature ON request_statistics (signature)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE request_statistics');
    }
}
