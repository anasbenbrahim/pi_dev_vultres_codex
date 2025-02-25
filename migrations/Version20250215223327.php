<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215223327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande ADD offer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A553C674EE FOREIGN KEY (offer_id) REFERENCES offer (id)');
        $this->addSql('CREATE INDEX IDX_2694D7A553C674EE ON demande (offer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A553C674EE');
        $this->addSql('DROP INDEX IDX_2694D7A553C674EE ON demande');
        $this->addSql('ALTER TABLE demande DROP offer_id');
    }
}
