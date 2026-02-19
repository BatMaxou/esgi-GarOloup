<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260222110403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ip field to temp_user entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE temp_user ADD ip VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE temp_user DROP ip');
    }
}
