<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230125200841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Base de données initiale avec le compte WEBMASTER qui sera en charge de créer le compte du président';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE children (id INT AUTO_INCREMENT NOT NULL, registration_event_id INT DEFAULT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, classroom VARCHAR(20) DEFAULT NULL, INDEX IDX_A197B1BA4E029F03 (registration_event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(50) DEFAULT NULL, email VARCHAR(180) NOT NULL, subject VARCHAR(100) DEFAULT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_replied TINYINT(1) DEFAULT 0 NOT NULL, response LONGTEXT DEFAULT NULL, reply_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, location VARCHAR(100) NOT NULL, price INT DEFAULT NULL, starts_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', finish_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(255) NOT NULL, capacity INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, help_needed TINYINT(1) DEFAULT NULL, published TINYINT(1) NOT NULL, image_name VARCHAR(255) DEFAULT \'event.jpeg\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', registered INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_creneau (event_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', slot_id INT NOT NULL, INDEX IDX_7C3122F071F7E88B (event_id), INDEX IDX_7C3122F059E5119C (slot_id), PRIMARY KEY(event_id, slot_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', registration_event_id INT NOT NULL, event_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', stripe_session_id LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', stripe_payment_intent_id VARCHAR(255) NOT NULL, stripe_payment_intent_status VARCHAR(100) NOT NULL, reserved_places INT NOT NULL, amount INT NOT NULL, unit_price INT NOT NULL, INDEX IDX_6D28840D4E029F03 (registration_event_id), INDEX IDX_6D28840D71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registration_event (id INT AUTO_INCREMENT NOT NULL, event_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, telephone VARCHAR(10) DEFAULT NULL, paid TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B404AA4F71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registration_help (id INT AUTO_INCREMENT NOT NULL, event_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, telephone VARCHAR(10) DEFAULT NULL, activity VARCHAR(150) NOT NULL, message LONGTEXT DEFAULT NULL, creneau_choices LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_C7B94B7071F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE slot (id INT AUTO_INCREMENT NOT NULL, starts_at TIME NOT NULL, ends_at TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, pseudo VARCHAR(50) DEFAULT NULL, telephone VARCHAR(10) DEFAULT NULL, address VARCHAR(150) DEFAULT NULL, complement_address VARCHAR(200) DEFAULT NULL, postal_code VARCHAR(5) DEFAULT NULL, town VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', birthday DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', function VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE children ADD CONSTRAINT FK_A197B1BA4E029F03 FOREIGN KEY (registration_event_id) REFERENCES registration_event (id)');
        $this->addSql('ALTER TABLE event_creneau ADD CONSTRAINT FK_7C3122F071F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_creneau ADD CONSTRAINT FK_7C3122F059E5119C FOREIGN KEY (slot_id) REFERENCES slot (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D4E029F03 FOREIGN KEY (registration_event_id) REFERENCES registration_event (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE registration_event ADD CONSTRAINT FK_B404AA4F71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE registration_help ADD CONSTRAINT FK_C7B94B7071F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE children DROP FOREIGN KEY FK_A197B1BA4E029F03');
        $this->addSql('ALTER TABLE event_creneau DROP FOREIGN KEY FK_7C3122F071F7E88B');
        $this->addSql('ALTER TABLE event_creneau DROP FOREIGN KEY FK_7C3122F059E5119C');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D4E029F03');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D71F7E88B');
        $this->addSql('ALTER TABLE registration_event DROP FOREIGN KEY FK_B404AA4F71F7E88B');
        $this->addSql('ALTER TABLE registration_help DROP FOREIGN KEY FK_C7B94B7071F7E88B');
        $this->addSql('DROP TABLE children');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_creneau');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE registration_event');
        $this->addSql('DROP TABLE registration_help');
        $this->addSql('DROP TABLE slot');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
