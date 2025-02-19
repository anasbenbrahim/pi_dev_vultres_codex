<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216232618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE demande_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE offer_id_seq CASCADE');
        $this->addSql('ALTER TABLE demande DROP CONSTRAINT fk_2694d7a553c674ee');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE demande');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE demande_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE offer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE offer (id INT NOT NULL, nom VARCHAR(255) NOT NULL, domain VARCHAR(255) NOT NULL, date_offer DATE NOT NULL, description VARCHAR(255) NOT NULL, nb_places INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE demande (id INT NOT NULL, offer_id INT DEFAULT NULL, service VARCHAR(255) NOT NULL, date_demande DATE NOT NULL, cv VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_2694d7a553c674ee ON demande (offer_id)');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT fk_2694d7a553c674ee FOREIGN KEY (offer_id) REFERENCES offer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
