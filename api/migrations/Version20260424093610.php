<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260424093610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE time_up_game_event (id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE villager_setup_event (target_player_id VARCHAR(36) NOT NULL, id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE time_up_game_event ADD CONSTRAINT FK_56568D2BF396750 FOREIGN KEY (id) REFERENCES game_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE villager_setup_event ADD CONSTRAINT FK_CBD60EC5BF396750 FOREIGN KEY (id) REFERENCES game_event (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE time_up_game_event DROP FOREIGN KEY FK_56568D2BF396750');
        $this->addSql('ALTER TABLE villager_setup_event DROP FOREIGN KEY FK_CBD60EC5BF396750');
        $this->addSql('DROP TABLE time_up_game_event');
        $this->addSql('DROP TABLE villager_setup_event');
    }
}
