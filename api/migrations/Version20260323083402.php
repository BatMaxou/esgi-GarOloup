<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260323083402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add SetGameMaster event';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE set_game_master_event (target_player_id VARCHAR(255) NOT NULL, id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE set_game_master_event ADD CONSTRAINT FK_CD7A9ACEBF396750 FOREIGN KEY (id) REFERENCES game_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game ADD game_master_id BINARY(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CC1151A13 FOREIGN KEY (game_master_id) REFERENCES player (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_232B318CC1151A13 ON game (game_master_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE set_game_master_event DROP FOREIGN KEY FK_CD7A9ACEBF396750');
        $this->addSql('DROP TABLE set_game_master_event');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CC1151A13');
        $this->addSql('DROP INDEX UNIQ_232B318CC1151A13 ON game');
        $this->addSql('ALTER TABLE game DROP game_master_id');
    }
}
