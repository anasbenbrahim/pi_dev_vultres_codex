<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217213557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipements DROP CONSTRAINT fk_3f02d86b670c757f');
        $this->addSql('DROP INDEX idx_3f02d86b670c757f');
        $this->addSql('ALTER TABLE equipements ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE equipements DROP fournisseur_id');
        $this->addSql('ALTER TABLE equipements ADD CONSTRAINT FK_3F02D86BA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3F02D86BA76ED395 ON equipements (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE equipements DROP CONSTRAINT FK_3F02D86BA76ED395');
        $this->addSql('DROP INDEX IDX_3F02D86BA76ED395');
        $this->addSql('ALTER TABLE equipements ADD fournisseur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE equipements DROP user_id');
        $this->addSql('ALTER TABLE equipements ADD CONSTRAINT fk_3f02d86b670c757f FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_3f02d86b670c757f ON equipements (fournisseur_id)');
    }
}
