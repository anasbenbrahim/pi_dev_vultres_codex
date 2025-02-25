<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215223132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee ADD employeedemande_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1C8E59747 FOREIGN KEY (employeedemande_id) REFERENCES demande (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D9F75A1C8E59747 ON employee (employeedemande_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1C8E59747');
        $this->addSql('DROP INDEX UNIQ_5D9F75A1C8E59747 ON employee');
        $this->addSql('ALTER TABLE employee DROP employeedemande_id');
    }
}
