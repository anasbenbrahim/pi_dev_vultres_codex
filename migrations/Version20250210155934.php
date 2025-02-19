<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210155934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id INT NOT NULL, address VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE employee (id INT NOT NULL, department VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE fermier (id INT NOT NULL, farm_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE fournisseur (id INT NOT NULL, company_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fermier ADD CONSTRAINT FK_86EADB64BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fournisseur ADD CONSTRAINT FK_369ECA32BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD discr VARCHAR(255)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C7440455BF396750');
        $this->addSql('ALTER TABLE employee DROP CONSTRAINT FK_5D9F75A1BF396750');
        $this->addSql('ALTER TABLE fermier DROP CONSTRAINT FK_86EADB64BF396750');
        $this->addSql('ALTER TABLE fournisseur DROP CONSTRAINT FK_369ECA32BF396750');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE fermier');
        $this->addSql('DROP TABLE fournisseur');
        $this->addSql('ALTER TABLE "user" DROP discr');
    }
}
