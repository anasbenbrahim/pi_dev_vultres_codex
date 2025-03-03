<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250303034119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE category_equipements_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE commentaire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE demande_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE equipements_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE event_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE marche_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE notification_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE offer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE order_item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE orders_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE produit_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE publication_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rating_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reclamation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE category_equipements (id INT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE client (id INT NOT NULL, address VARCHAR(255) DEFAULT NULL, is_banned BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE commentaire (id INT NOT NULL, publication_id INT DEFAULT NULL, client_id INT NOT NULL, description VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_67F068BC38B217A7 ON commentaire (publication_id)');
        $this->addSql('CREATE INDEX IDX_67F068BC19EB6921 ON commentaire (client_id)');
        $this->addSql('CREATE TABLE demande (id INT NOT NULL, offer_id INT DEFAULT NULL, service VARCHAR(255) NOT NULL, date_demande DATE NOT NULL, cv VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2694D7A553C674EE ON demande (offer_id)');
        $this->addSql('CREATE TABLE employee (id INT NOT NULL, department VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE equipements (id INT NOT NULL, category_id INT DEFAULT NULL, user_id INT NOT NULL, nom VARCHAR(255) NOT NULL, quantite INT NOT NULL, prix DOUBLE PRECISION NOT NULL, description VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3F02D86B12469DE2 ON equipements (category_id)');
        $this->addSql('CREATE INDEX IDX_3F02D86BA76ED395 ON equipements (user_id)');
        $this->addSql('CREATE TABLE event (id INT NOT NULL, nom VARCHAR(255) NOT NULL, descr VARCHAR(255) NOT NULL, date DATE NOT NULL, type VARCHAR(255) NOT NULL, photo VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE fermier (id INT NOT NULL, farm_name VARCHAR(255) DEFAULT NULL, is_banned BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE fournisseur (id INT NOT NULL, company_name VARCHAR(255) DEFAULT NULL, is_banned BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE marche (id INT NOT NULL, id_user INT NOT NULL, prodid INT NOT NULL, nomprod VARCHAR(150) NOT NULL, prix DOUBLE PRECISION NOT NULL, dateajout DATE NOT NULL, quantite DOUBLE PRECISION NOT NULL, image VARCHAR(150) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE notification (id INT NOT NULL, publication_id INT NOT NULL, client_id INT NOT NULL, commentaire_id INT DEFAULT NULL, message VARCHAR(255) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, reading BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF5476CA38B217A7 ON notification (publication_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA19EB6921 ON notification (client_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CABA9CD190 ON notification (commentaire_id)');
        $this->addSql('CREATE TABLE offer (id INT NOT NULL, nom VARCHAR(255) NOT NULL, domain VARCHAR(255) NOT NULL, date_offer DATE NOT NULL, description VARCHAR(255) NOT NULL, nb_places INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE order_item (id INT NOT NULL, order_id INT NOT NULL, produit_id INT DEFAULT NULL, quantite INT NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_52EA1F098D9F6D38 ON order_item (order_id)');
        $this->addSql('CREATE INDEX IDX_52EA1F09F347EFB ON order_item (produit_id)');
        $this->addSql('CREATE TABLE orders (id INT NOT NULL, user_id INT DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, total DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E52FFDEEA76ED395 ON orders (user_id)');
        $this->addSql('CREATE TABLE produit (id INT NOT NULL, user_id INT NOT NULL, category_id INT NOT NULL, nomprod VARCHAR(50) NOT NULL, image VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, quantite INT NOT NULL, descr VARCHAR(255) NOT NULL, status BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_29A5EC27A76ED395 ON produit (user_id)');
        $this->addSql('CREATE INDEX IDX_29A5EC2712469DE2 ON produit (category_id)');
        $this->addSql('CREATE TABLE publication (id INT NOT NULL, client_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description TEXT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AF3C677919EB6921 ON publication (client_id)');
        $this->addSql('CREATE TABLE rating (id INT NOT NULL, client_id INT NOT NULL, publication_id INT NOT NULL, rating INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D889262219EB6921 ON rating (client_id)');
        $this->addSql('CREATE INDEX IDX_D889262238B217A7 ON rating (publication_id)');
        $this->addSql('CREATE TABLE reclamation (id INT NOT NULL, publication_id INT NOT NULL, client_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CE60640438B217A7 ON reclamation (publication_id)');
        $this->addSql('CREATE INDEX IDX_CE60640419EB6921 ON reclamation (client_id)');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE superadmin (id INT NOT NULL, code VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC38B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A553C674EE FOREIGN KEY (offer_id) REFERENCES offer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE equipements ADD CONSTRAINT FK_3F02D86B12469DE2 FOREIGN KEY (category_id) REFERENCES category_equipements (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE equipements ADD CONSTRAINT FK_3F02D86BA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fermier ADD CONSTRAINT FK_86EADB64BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fournisseur ADD CONSTRAINT FK_369ECA32BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA38B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CABA9CD190 FOREIGN KEY (commentaire_id) REFERENCES commentaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2712469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C677919EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D889262219EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D889262238B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE60640438B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE60640419EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE superadmin ADD CONSTRAINT FK_39D87404BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE category_equipements_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE commentaire_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE demande_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE equipements_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE event_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE marche_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE notification_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE offer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE order_item_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE orders_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE produit_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE publication_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rating_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reclamation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C7440455BF396750');
        $this->addSql('ALTER TABLE commentaire DROP CONSTRAINT FK_67F068BC38B217A7');
        $this->addSql('ALTER TABLE commentaire DROP CONSTRAINT FK_67F068BC19EB6921');
        $this->addSql('ALTER TABLE demande DROP CONSTRAINT FK_2694D7A553C674EE');
        $this->addSql('ALTER TABLE employee DROP CONSTRAINT FK_5D9F75A1BF396750');
        $this->addSql('ALTER TABLE equipements DROP CONSTRAINT FK_3F02D86B12469DE2');
        $this->addSql('ALTER TABLE equipements DROP CONSTRAINT FK_3F02D86BA76ED395');
        $this->addSql('ALTER TABLE fermier DROP CONSTRAINT FK_86EADB64BF396750');
        $this->addSql('ALTER TABLE fournisseur DROP CONSTRAINT FK_369ECA32BF396750');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CA38B217A7');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CA19EB6921');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CABA9CD190');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F09F347EFB');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEEA76ED395');
        $this->addSql('ALTER TABLE produit DROP CONSTRAINT FK_29A5EC27A76ED395');
        $this->addSql('ALTER TABLE produit DROP CONSTRAINT FK_29A5EC2712469DE2');
        $this->addSql('ALTER TABLE publication DROP CONSTRAINT FK_AF3C677919EB6921');
        $this->addSql('ALTER TABLE rating DROP CONSTRAINT FK_D889262219EB6921');
        $this->addSql('ALTER TABLE rating DROP CONSTRAINT FK_D889262238B217A7');
        $this->addSql('ALTER TABLE reclamation DROP CONSTRAINT FK_CE60640438B217A7');
        $this->addSql('ALTER TABLE reclamation DROP CONSTRAINT FK_CE60640419EB6921');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE superadmin DROP CONSTRAINT FK_39D87404BF396750');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_equipements');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE demande');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE equipements');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE fermier');
        $this->addSql('DROP TABLE fournisseur');
        $this->addSql('DROP TABLE marche');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE publication');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE superadmin');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
