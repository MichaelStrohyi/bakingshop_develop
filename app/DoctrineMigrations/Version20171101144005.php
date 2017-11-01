<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171101144005 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, width SMALLINT NOT NULL, height SMALLINT NOT NULL, size INT NOT NULL, filename VARCHAR(255) NOT NULL, mime VARCHAR(32) NOT NULL, updated_at DATETIME NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coupon CHANGE activity activity SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE menu_item CHANGE position position INT NOT NULL');
        $this->addSql('ALTER TABLE store ADD logo INT DEFAULT NULL');
        $this->addSql('ALTER TABLE store ADD CONSTRAINT FK_FF575877E48E9A13 FOREIGN KEY (logo) REFERENCES image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FF575877E48E9A13 ON store (logo)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE store DROP FOREIGN KEY FK_FF575877E48E9A13');
        $this->addSql('DROP TABLE image');
        $this->addSql('ALTER TABLE coupon CHANGE activity activity SMALLINT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE menu_item CHANGE position position INT DEFAULT 10000 NOT NULL');
        $this->addSql('DROP INDEX UNIQ_FF575877E48E9A13 ON store');
        $this->addSql('ALTER TABLE store DROP logo');
    }
}
