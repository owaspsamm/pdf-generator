<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221109092441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE `activity` (id INT AUTO_INCREMENT NOT NULL, practice_level_id INT DEFAULT NULL, stream_id INT DEFAULT NULL, `title` VARCHAR(255) DEFAULT NULL, `benefit` VARCHAR(255) DEFAULT NULL, `short_description` LONGTEXT DEFAULT NULL, `long_description` LONGTEXT DEFAULT NULL, `notes` LONGTEXT DEFAULT NULL, `external_id` VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, INDEX IDX_AC74095A58D2F8A2 (practice_level_id), INDEX IDX_AC74095AD0ED463E (stream_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE `answer` (id INT AUTO_INCREMENT NOT NULL, answer_set_id INT DEFAULT NULL, `text` VARCHAR(255) DEFAULT NULL, `value` NUMERIC(10, 2) NOT NULL, `weight` NUMERIC(10, 2) NOT NULL, `order` INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, INDEX IDX_DADD4A25E20237BF (answer_set_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE `answer_set` (id INT AUTO_INCREMENT NOT NULL, `external_id` VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE `business_function` (id INT AUTO_INCREMENT NOT NULL, `name` VARCHAR(255) DEFAULT NULL, `description` LONGTEXT DEFAULT NULL, `color` VARCHAR(255) DEFAULT NULL, `logo` VARCHAR(255) DEFAULT NULL, `order` INT NOT NULL, `external_id` VARCHAR(255) DEFAULT NULL, `icon` VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE `maturity_level` (id INT AUTO_INCREMENT NOT NULL, `level` INT NOT NULL, `description` LONGTEXT DEFAULT NULL, `external_id` VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE `practice` (id INT AUTO_INCREMENT NOT NULL, business_function_id INT DEFAULT NULL, `name` VARCHAR(255) DEFAULT NULL, `short_name` VARCHAR(255) DEFAULT NULL, `short_description` LONGTEXT DEFAULT NULL, `long_description` LONGTEXT DEFAULT NULL, `order` INT NOT NULL, `external_id` VARCHAR(255) DEFAULT NULL, `icon` VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, INDEX IDX_7FEC344E26C05169 (business_function_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE `practice_level` (id INT AUTO_INCREMENT NOT NULL, maturity_level_id INT DEFAULT NULL, practice_id INT DEFAULT NULL, `objective` VARCHAR(255) DEFAULT NULL, `external_id` VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, INDEX IDX_B225843861FD714C (maturity_level_id), INDEX IDX_B2258438ED33821 (practice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE `question` (id INT AUTO_INCREMENT NOT NULL, activity_id INT DEFAULT NULL, answer_set_id INT DEFAULT NULL, `text` LONGTEXT DEFAULT NULL, `order` INT NOT NULL, `quality` LONGTEXT DEFAULT NULL, `external_id` VARCHAR(255) DEFAULT NULL, `weight` NUMERIC(10, 2) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, INDEX IDX_B6F7494E81C06096 (activity_id), INDEX IDX_B6F7494EE20237BF (answer_set_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE `stream` (id INT AUTO_INCREMENT NOT NULL, practice_id INT DEFAULT NULL, `name` VARCHAR(255) DEFAULT NULL, `description` LONGTEXT DEFAULT NULL, `order` INT NOT NULL, `external_id` VARCHAR(255) DEFAULT NULL, `weight` NUMERIC(10, 2) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, INDEX IDX_F0E9BE1CED33821 (practice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE `activity` ADD CONSTRAINT FK_AC74095A58D2F8A2 FOREIGN KEY (practice_level_id) REFERENCES `practice_level` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `activity` ADD CONSTRAINT FK_AC74095AD0ED463E FOREIGN KEY (stream_id) REFERENCES `stream` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `answer` ADD CONSTRAINT FK_DADD4A25E20237BF FOREIGN KEY (answer_set_id) REFERENCES `answer_set` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `practice` ADD CONSTRAINT FK_7FEC344E26C05169 FOREIGN KEY (business_function_id) REFERENCES `business_function` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `practice_level` ADD CONSTRAINT FK_B225843861FD714C FOREIGN KEY (maturity_level_id) REFERENCES `maturity_level` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `practice_level` ADD CONSTRAINT FK_B2258438ED33821 FOREIGN KEY (practice_id) REFERENCES `practice` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `question` ADD CONSTRAINT FK_B6F7494E81C06096 FOREIGN KEY (activity_id) REFERENCES `activity` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `question` ADD CONSTRAINT FK_B6F7494EE20237BF FOREIGN KEY (answer_set_id) REFERENCES `answer_set` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `stream` ADD CONSTRAINT FK_F0E9BE1CED33821 FOREIGN KEY (practice_id) REFERENCES `practice` (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `activity` DROP FOREIGN KEY FK_AC74095A58D2F8A2');
        $this->addSql('ALTER TABLE `activity` DROP FOREIGN KEY FK_AC74095AD0ED463E');
        $this->addSql('ALTER TABLE `answer` DROP FOREIGN KEY FK_DADD4A25E20237BF');
        $this->addSql('ALTER TABLE `practice` DROP FOREIGN KEY FK_7FEC344E26C05169');
        $this->addSql('ALTER TABLE `practice_level` DROP FOREIGN KEY FK_B225843861FD714C');
        $this->addSql('ALTER TABLE `practice_level` DROP FOREIGN KEY FK_B2258438ED33821');
        $this->addSql('ALTER TABLE `question` DROP FOREIGN KEY FK_B6F7494E81C06096');
        $this->addSql('ALTER TABLE `question` DROP FOREIGN KEY FK_B6F7494EE20237BF');
        $this->addSql('ALTER TABLE `stream` DROP FOREIGN KEY FK_F0E9BE1CED33821');
        $this->addSql('DROP TABLE `activity`');
        $this->addSql('DROP TABLE `answer`');
        $this->addSql('DROP TABLE `answer_set`');
        $this->addSql('DROP TABLE `business_function`');
        $this->addSql('DROP TABLE `maturity_level`');
        $this->addSql('DROP TABLE `practice`');
        $this->addSql('DROP TABLE `practice_level`');
        $this->addSql('DROP TABLE `question`');
        $this->addSql('DROP TABLE `stream`');
    }
}
