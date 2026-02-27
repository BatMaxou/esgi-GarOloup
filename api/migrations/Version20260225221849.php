<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260225221849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Timestampable entities';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE game ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE player ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD game_id BINARY(16) DEFAULT NULL, CHANGE is_dead dead TINYINT NOT NULL');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('CREATE INDEX IDX_98197A65E48FD905 ON player (game_id)');
        $this->addSql('ALTER TABLE temp_user ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE game DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65E48FD905');
        $this->addSql('DROP INDEX IDX_98197A65E48FD905 ON player');
        $this->addSql('ALTER TABLE player DROP created_at, DROP updated_at, DROP game_id, CHANGE dead is_dead TINYINT NOT NULL');
        $this->addSql('ALTER TABLE temp_user DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE user DROP created_at, DROP updated_at');
    }
}
