<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220161945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52BB8CB1F8 FOREIGN KEY (fermier_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_8B27C52BB8CB1F8 ON devis (fermier_id)');
        $this->addSql('ALTER TABLE reponse_devis ADD fournisseur_id INT NOT NULL');
        $this->addSql('ALTER TABLE reponse_devis ADD CONSTRAINT FK_64CEBE48670C757F FOREIGN KEY (fournisseur_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_64CEBE48670C757F ON reponse_devis (fournisseur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis DROP FOREIGN KEY FK_8B27C52BB8CB1F8');
        $this->addSql('DROP INDEX IDX_8B27C52BB8CB1F8 ON devis');
        $this->addSql('ALTER TABLE reponse_devis DROP FOREIGN KEY FK_64CEBE48670C757F');
        $this->addSql('DROP INDEX IDX_64CEBE48670C757F ON reponse_devis');
        $this->addSql('ALTER TABLE reponse_devis DROP fournisseur_id');
    }
}
