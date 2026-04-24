<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260424093525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game ADD step_end_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE player ADD afk_count INT NOT NULL');
        $this->addSql('ALTER TABLE villager_role ADD friend_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP step_end_at');
        $this->addSql('ALTER TABLE player DROP afk_count');
        $this->addSql('ALTER TABLE villager_role DROP friend_id');
    }
}
