<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250214233306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP image');
        $this->addSql('ALTER TABLE fermier DROP image');
        $this->addSql('ALTER TABLE fournisseur DROP image');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE fournisseur ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fermier ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD image VARCHAR(255) DEFAULT NULL');
    }
}
