<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216184222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE commentaire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE publication_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reclamation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE commentaire (id INT NOT NULL, publication_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_67F068BC38B217A7 ON commentaire (publication_id)');
        $this->addSql('CREATE TABLE publication (id INT NOT NULL, client_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description TEXT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AF3C677919EB6921 ON publication (client_id)');
        $this->addSql('CREATE TABLE reclamation (id INT NOT NULL, publication_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CE60640438B217A7 ON reclamation (publication_id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC38B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C677919EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE60640438B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE commentaire_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE publication_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reclamation_id_seq CASCADE');
        $this->addSql('ALTER TABLE commentaire DROP CONSTRAINT FK_67F068BC38B217A7');
        $this->addSql('ALTER TABLE publication DROP CONSTRAINT FK_AF3C677919EB6921');
        $this->addSql('ALTER TABLE reclamation DROP CONSTRAINT FK_CE60640438B217A7');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE publication');
        $this->addSql('DROP TABLE reclamation');
    }
}
