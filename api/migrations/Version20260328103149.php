<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260328103149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'WIP - First migration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE admin (id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE close_game_invitation_event (id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE create_game_event (id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE game (step VARCHAR(255) NOT NULL, join_code VARCHAR(8) NOT NULL, id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, host_id BINARY(16) NOT NULL, game_master_id BINARY(16) DEFAULT NULL, UNIQUE INDEX UNIQ_232B318C1FB8D185 (host_id), UNIQUE INDEX UNIQ_232B318CC1151A13 (game_master_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE game_event (game_id VARCHAR(255) NOT NULL, player_username VARCHAR(255) NOT NULL, id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE join_game_event (id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE player (dead TINYINT NOT NULL, id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id BINARY(16) DEFAULT NULL, temp_user_id BINARY(16) DEFAULT NULL, game_id BINARY(16) DEFAULT NULL, INDEX IDX_98197A65A76ED395 (user_id), INDEX IDX_98197A651FA4E70A (temp_user_id), INDEX IDX_98197A65E48FD905 (game_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE re_open_game_invitation_event (id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE refresh_token (refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX UNIQ_C74F2195C74F2195 (refresh_token), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE role (type VARCHAR(255) DEFAULT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, ability LONGTEXT DEFAULT NULL, picture_name VARCHAR(255) DEFAULT NULL, min_players INT DEFAULT NULL, max_per_game INT DEFAULT NULL, teams LONGTEXT NOT NULL, id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_57698A6A8CDE5729 (type), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE set_game_master_event (target_player_id VARCHAR(255) NOT NULL, id BINARY(16) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE temp_user (username VARCHAR(255) NOT NULL, id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, roles JSON NOT NULL, ip VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (username VARCHAR(255) NOT NULL, id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, roles JSON NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE close_game_invitation_event ADD CONSTRAINT FK_9E6235E7BF396750 FOREIGN KEY (id) REFERENCES game_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE create_game_event ADD CONSTRAINT FK_1AD0C1CCBF396750 FOREIGN KEY (id) REFERENCES game_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C1FB8D185 FOREIGN KEY (host_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CC1151A13 FOREIGN KEY (game_master_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE join_game_event ADD CONSTRAINT FK_61FF5DD1BF396750 FOREIGN KEY (id) REFERENCES game_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A651FA4E70A FOREIGN KEY (temp_user_id) REFERENCES temp_user (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE re_open_game_invitation_event ADD CONSTRAINT FK_8337E0E5BF396750 FOREIGN KEY (id) REFERENCES game_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE set_game_master_event ADD CONSTRAINT FK_CD7A9ACEBF396750 FOREIGN KEY (id) REFERENCES game_event (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76BF396750');
        $this->addSql('ALTER TABLE close_game_invitation_event DROP FOREIGN KEY FK_9E6235E7BF396750');
        $this->addSql('ALTER TABLE create_game_event DROP FOREIGN KEY FK_1AD0C1CCBF396750');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C1FB8D185');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CC1151A13');
        $this->addSql('ALTER TABLE join_game_event DROP FOREIGN KEY FK_61FF5DD1BF396750');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65A76ED395');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A651FA4E70A');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65E48FD905');
        $this->addSql('ALTER TABLE re_open_game_invitation_event DROP FOREIGN KEY FK_8337E0E5BF396750');
        $this->addSql('ALTER TABLE set_game_master_event DROP FOREIGN KEY FK_CD7A9ACEBF396750');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE close_game_invitation_event');
        $this->addSql('DROP TABLE create_game_event');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_event');
        $this->addSql('DROP TABLE join_game_event');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE re_open_game_invitation_event');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE set_game_master_event');
        $this->addSql('DROP TABLE temp_user');
        $this->addSql('DROP TABLE user');
    }
}
