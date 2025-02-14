<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250214122211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipements DROP FOREIGN KEY FK_3F02D86B12469DE2');
        $this->addSql('ALTER TABLE equipements CHANGE category_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE equipements ADD CONSTRAINT FK_3F02D86B12469DE2 FOREIGN KEY (category_id) REFERENCES category_equipements (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipements DROP FOREIGN KEY FK_3F02D86B12469DE2');
        $this->addSql('ALTER TABLE equipements CHANGE category_id category_id INT NOT NULL');
        $this->addSql('ALTER TABLE equipements ADD CONSTRAINT FK_3F02D86B12469DE2 FOREIGN KEY (category_id) REFERENCES category_equipements (id)');
    }
}
