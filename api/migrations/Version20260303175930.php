<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260303175930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add reset token to user.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD reset_token VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP reset_token');
    }
}
