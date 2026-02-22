<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260219203726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Base Game entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE player (is_dead TINYINT NOT NULL, id BINARY(16) NOT NULL, user_id BINARY(16) DEFAULT NULL, temp_user_id BINARY(16) DEFAULT NULL, INDEX IDX_98197A65A76ED395 (user_id), INDEX IDX_98197A651FA4E70A (temp_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A651FA4E70A FOREIGN KEY (temp_user_id) REFERENCES temp_user (id)');
        $this->addSql('ALTER TABLE game ADD join_code VARCHAR(8) NOT NULL, ADD host_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C1FB8D185 FOREIGN KEY (host_id) REFERENCES player (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_232B318C1FB8D185 ON game (host_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65A76ED395');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A651FA4E70A');
        $this->addSql('DROP TABLE player');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C1FB8D185');
        $this->addSql('DROP INDEX UNIQ_232B318C1FB8D185 ON game');
        $this->addSql('ALTER TABLE game DROP join_code, DROP host_id');
    }
}
