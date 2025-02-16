<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216223157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE demande_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE offer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE demande (id INT NOT NULL, offer_id INT DEFAULT NULL, service VARCHAR(255) NOT NULL, date_demande DATE NOT NULL, cv VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2694D7A553C674EE ON demande (offer_id)');
        $this->addSql('CREATE TABLE offer (id INT NOT NULL, fermieroffer_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, domain VARCHAR(255) NOT NULL, date_offer DATE NOT NULL, description VARCHAR(255) NOT NULL, nb_places INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_29D6873E27DAF0C6 ON offer (fermieroffer_id)');
        $this->addSql('CREATE TABLE offer_employee (offer_id INT NOT NULL, employee_id INT NOT NULL, PRIMARY KEY(offer_id, employee_id))');
        $this->addSql('CREATE INDEX IDX_24E2C5C353C674EE ON offer_employee (offer_id)');
        $this->addSql('CREATE INDEX IDX_24E2C5C38C03F15C ON offer_employee (employee_id)');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A553C674EE FOREIGN KEY (offer_id) REFERENCES offer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873E27DAF0C6 FOREIGN KEY (fermieroffer_id) REFERENCES fermier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE offer_employee ADD CONSTRAINT FK_24E2C5C353C674EE FOREIGN KEY (offer_id) REFERENCES offer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE offer_employee ADD CONSTRAINT FK_24E2C5C38C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE demande_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE offer_id_seq CASCADE');
        $this->addSql('ALTER TABLE demande DROP CONSTRAINT FK_2694D7A553C674EE');
        $this->addSql('ALTER TABLE offer DROP CONSTRAINT FK_29D6873E27DAF0C6');
        $this->addSql('ALTER TABLE offer_employee DROP CONSTRAINT FK_24E2C5C353C674EE');
        $this->addSql('ALTER TABLE offer_employee DROP CONSTRAINT FK_24E2C5C38C03F15C');
        $this->addSql('DROP TABLE demande');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE offer_employee');
    }
}
