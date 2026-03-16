<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260315202920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Game events';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE close_invitation_event (id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE create_game_event (id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE game_event (game_id VARCHAR(255) NOT NULL, player_username VARCHAR(255) NOT NULL, id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE join_game_event (id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE re_open_invitation_event (id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE close_invitation_event ADD CONSTRAINT FK_3BDBDCDBBF396750 FOREIGN KEY (id) REFERENCES game_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE create_game_event ADD CONSTRAINT FK_1AD0C1CCBF396750 FOREIGN KEY (id) REFERENCES game_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE join_game_event ADD CONSTRAINT FK_61FF5DD1BF396750 FOREIGN KEY (id) REFERENCES game_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE re_open_invitation_event ADD CONSTRAINT FK_82ED391BBF396750 FOREIGN KEY (id) REFERENCES game_event (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE close_invitation_event DROP FOREIGN KEY FK_3BDBDCDBBF396750');
        $this->addSql('ALTER TABLE create_game_event DROP FOREIGN KEY FK_1AD0C1CCBF396750');
        $this->addSql('ALTER TABLE join_game_event DROP FOREIGN KEY FK_61FF5DD1BF396750');
        $this->addSql('ALTER TABLE re_open_invitation_event DROP FOREIGN KEY FK_82ED391BBF396750');
        $this->addSql('DROP TABLE close_invitation_event');
        $this->addSql('DROP TABLE create_game_event');
        $this->addSql('DROP TABLE game_event');
        $this->addSql('DROP TABLE join_game_event');
        $this->addSql('DROP TABLE re_open_invitation_event');
    }
}
