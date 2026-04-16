<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260414151455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Separate game steps';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE game ADD runtime_step VARCHAR(255) DEFAULT NULL, CHANGE step initialisation_step VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE game DROP runtime_step, CHANGE initialisation_step step VARCHAR(255) NOT NULL');
    }
}
