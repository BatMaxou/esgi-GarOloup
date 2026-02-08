<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260208182451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'TEST MIGRATION';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, step VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE game');
    }
}
