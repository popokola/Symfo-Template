<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221201190634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE mission_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE type_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE accessorize_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE location_id_seq CASCADE');
        $this->addSql('ALTER TABLE accessorize DROP CONSTRAINT fk_18849d8f12469de2');
        $this->addSql('ALTER TABLE mission DROP CONSTRAINT fk_9067f23cc54c8c93');
        $this->addSql('ALTER TABLE mission DROP CONSTRAINT fk_9067f23c64d218e');
        $this->addSql('ALTER TABLE mission_user DROP CONSTRAINT fk_a4d17a46be6cae90');
        $this->addSql('ALTER TABLE mission_user DROP CONSTRAINT fk_a4d17a46a76ed395');
        $this->addSql('ALTER TABLE mission_accessorize DROP CONSTRAINT fk_7f1bd76ebe6cae90');
        $this->addSql('ALTER TABLE mission_accessorize DROP CONSTRAINT fk_7f1bd76ea2122afb');
        $this->addSql('DROP TABLE accessorize');
        $this->addSql('DROP TABLE mission');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE mission_user');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE mission_accessorize');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE mission_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE accessorize_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE location_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE accessorize (id INT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_18849d8f12469de2 ON accessorize (category_id)');
        $this->addSql('CREATE TABLE mission (id INT NOT NULL, type_id INT DEFAULT NULL, location_id INT DEFAULT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(100) NOT NULL, description TEXT NOT NULL, slug VARCHAR(155) NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_9067f23c64d218e ON mission (location_id)');
        $this->addSql('CREATE INDEX idx_9067f23cc54c8c93 ON mission (type_id)');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE mission_user (mission_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(mission_id, user_id))');
        $this->addSql('CREATE INDEX idx_a4d17a46a76ed395 ON mission_user (user_id)');
        $this->addSql('CREATE INDEX idx_a4d17a46be6cae90 ON mission_user (mission_id)');
        $this->addSql('CREATE TABLE location (id INT NOT NULL, address VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE type (id INT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE mission_accessorize (mission_id INT NOT NULL, accessorize_id INT NOT NULL, PRIMARY KEY(mission_id, accessorize_id))');
        $this->addSql('CREATE INDEX idx_7f1bd76ea2122afb ON mission_accessorize (accessorize_id)');
        $this->addSql('CREATE INDEX idx_7f1bd76ebe6cae90 ON mission_accessorize (mission_id)');
        $this->addSql('ALTER TABLE accessorize ADD CONSTRAINT fk_18849d8f12469de2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT fk_9067f23cc54c8c93 FOREIGN KEY (type_id) REFERENCES type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT fk_9067f23c64d218e FOREIGN KEY (location_id) REFERENCES location (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mission_user ADD CONSTRAINT fk_a4d17a46be6cae90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mission_user ADD CONSTRAINT fk_a4d17a46a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mission_accessorize ADD CONSTRAINT fk_7f1bd76ebe6cae90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mission_accessorize ADD CONSTRAINT fk_7f1bd76ea2122afb FOREIGN KEY (accessorize_id) REFERENCES accessorize (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
