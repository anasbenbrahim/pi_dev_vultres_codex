<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227163219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fistival (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, discription VARCHAR(255) NOT NULL, date VARCHAR(255) NOT NULL, photo VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE simple (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, discription VARCHAR(255) NOT NULL, date DATE NOT NULL, photo VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workshop (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, discription VARCHAR(255) NOT NULL, date DATE NOT NULL, photo VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD latitude DOUBLE PRECISION NOT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL, DROP nom, DROP localisation');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE fistival');
        $this->addSql('DROP TABLE simple');
        $this->addSql('DROP TABLE workshop');
        $this->addSql('ALTER TABLE event ADD nom VARCHAR(255) NOT NULL, ADD localisation VARCHAR(255) NOT NULL, DROP latitude, DROP longitude');
    }
}
