<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215223009 extends AbstractMigration
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
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offer_employee DROP FOREIGN KEY FK_24E2C5C353C674EE');
        $this->addSql('ALTER TABLE offer_employee DROP FOREIGN KEY FK_24E2C5C38C03F15C');
        $this->addSql('DROP TABLE offer_employee');
    }
}
