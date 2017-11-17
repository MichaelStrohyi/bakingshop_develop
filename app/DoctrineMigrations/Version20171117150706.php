<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171117150706 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE operator (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coupon ADD verifiedAt DATE DEFAULT NULL, ADD subtype VARCHAR(255) DEFAULT NULL, ADD verifiedBy INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coupon ADD CONSTRAINT FK_64BF3F021C4DBF30 FOREIGN KEY (verifiedBy) REFERENCES operator (id)');
        $this->addSql('CREATE INDEX IDX_64BF3F021C4DBF30 ON coupon (verifiedBy)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coupon DROP FOREIGN KEY FK_64BF3F021C4DBF30');
        $this->addSql('DROP TABLE operator');
        $this->addSql('DROP INDEX IDX_64BF3F021C4DBF30 ON coupon');
        $this->addSql('ALTER TABLE coupon DROP verifiedAt, DROP subtype, DROP verifiedBy');
    }
}
