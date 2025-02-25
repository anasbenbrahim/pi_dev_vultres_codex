<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215222420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offer ADD fermieroffer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873E27DAF0C6 FOREIGN KEY (fermieroffer_id) REFERENCES fermier (id)');
        $this->addSql('CREATE INDEX IDX_29D6873E27DAF0C6 ON offer (fermieroffer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_29D6873E27DAF0C6');
        $this->addSql('DROP INDEX IDX_29D6873E27DAF0C6 ON offer');
        $this->addSql('ALTER TABLE offer DROP fermieroffer_id');
    }
}
