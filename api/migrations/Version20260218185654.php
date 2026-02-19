<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260218185654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Temp user base entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE temp_user (id BINARY(16) NOT NULL, roles JSON NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE temp_user');
    }
}
