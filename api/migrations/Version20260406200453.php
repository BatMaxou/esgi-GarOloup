<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260406200453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add max players, max time for discussion and public to game';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE create_game_event ADD max_players INT NOT NULL, ADD max_time_for_discussion INT NOT NULL, ADD public TINYINT NOT NULL');
        $this->addSql('ALTER TABLE game ADD max_players INT NOT NULL, ADD max_time_for_discussion INT NOT NULL, ADD public TINYINT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE create_game_event DROP max_players, DROP max_time_for_discussion, DROP public');
        $this->addSql('ALTER TABLE game DROP max_players, DROP max_time_for_discussion, DROP public');
    }
}
