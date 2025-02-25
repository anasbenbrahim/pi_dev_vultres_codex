<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250224195704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offer_employee (offer_id INT NOT NULL, employee_id INT NOT NULL, INDEX IDX_24E2C5C353C674EE (offer_id), INDEX IDX_24E2C5C38C03F15C (employee_id), PRIMARY KEY(offer_id, employee_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offer_employee ADD CONSTRAINT FK_24E2C5C353C674EE FOREIGN KEY (offer_id) REFERENCES offer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offer_employee ADD CONSTRAINT FK_24E2C5C38C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE demande ADD cv VARCHAR(255) NOT NULL, DROP cv_filename');
        $this->addSql('ALTER TABLE offer ADD fermieroffer_id INT DEFAULT NULL, CHANGE description description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873E27DAF0C6 FOREIGN KEY (fermieroffer_id) REFERENCES fermier (id)');
        $this->addSql('CREATE INDEX IDX_29D6873E27DAF0C6 ON offer (fermieroffer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offer_employee DROP FOREIGN KEY FK_24E2C5C353C674EE');
        $this->addSql('ALTER TABLE offer_employee DROP FOREIGN KEY FK_24E2C5C38C03F15C');
        $this->addSql('DROP TABLE offer_employee');
        $this->addSql('ALTER TABLE demande ADD cv_filename VARCHAR(255) DEFAULT NULL, DROP cv');
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_29D6873E27DAF0C6');
        $this->addSql('DROP INDEX IDX_29D6873E27DAF0C6 ON offer');
        $this->addSql('ALTER TABLE offer DROP fermieroffer_id, CHANGE description description LONGTEXT NOT NULL');
    }
}
