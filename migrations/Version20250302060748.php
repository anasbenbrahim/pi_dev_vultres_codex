<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302060748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client ADD is_banned TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE fermier ADD is_banned TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE fournisseur ADD is_banned TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP is_banned');
        $this->addSql('ALTER TABLE fermier DROP is_banned');
        $this->addSql('ALTER TABLE fournisseur DROP is_banned');
    }
}
