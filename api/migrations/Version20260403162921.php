<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260403162921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add game role dispatch event';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE game_role_dispatch_event (id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE game_role_dispatch_event ADD CONSTRAINT FK_87E7BF06BF396750 FOREIGN KEY (id) REFERENCES game_event (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE game_role_dispatch_event DROP FOREIGN KEY FK_87E7BF06BF396750');
        $this->addSql('DROP TABLE game_role_dispatch_event');
    }
}
