<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250222162551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis ADD fournisseur_id INT NOT NULL');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B670C757F FOREIGN KEY (fournisseur_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_8B27C52B670C757F ON devis (fournisseur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis DROP FOREIGN KEY FK_8B27C52B670C757F');
        $this->addSql('DROP INDEX IDX_8B27C52B670C757F ON devis');
        $this->addSql('ALTER TABLE devis DROP fournisseur_id');
    }
}
