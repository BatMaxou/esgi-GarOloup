<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260216213648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'User base entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE refresh_token (refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX UNIQ_C74F2195C74F2195 (refresh_token), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, id BINARY(16) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE user');
    }
}
