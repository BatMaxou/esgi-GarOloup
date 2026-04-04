<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260404125531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add lauch game event';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE launch_game_event (id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE launch_game_event ADD CONSTRAINT FK_5690F2A0BF396750 FOREIGN KEY (id) REFERENCES game_event (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE launch_game_event DROP FOREIGN KEY FK_5690F2A0BF396750');
        $this->addSql('DROP TABLE launch_game_event');
    }
}
