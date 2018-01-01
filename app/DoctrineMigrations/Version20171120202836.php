<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171120202836 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coupon DROP FOREIGN KEY FK_64BF3F021C4DBF30');
        $this->addSql('DROP INDEX IDX_64BF3F021C4DBF30 ON coupon');
        $this->addSql('ALTER TABLE coupon ADD discount VARCHAR(255) DEFAULT NULL, CHANGE verifiedby addedBy INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coupon ADD CONSTRAINT FK_64BF3F02E7CA843C FOREIGN KEY (addedBy) REFERENCES operator (id)');
        $this->addSql('CREATE INDEX IDX_64BF3F02E7CA843C ON coupon (addedBy)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coupon DROP FOREIGN KEY FK_64BF3F02E7CA843C');
        $this->addSql('DROP INDEX IDX_64BF3F02E7CA843C ON coupon');
        $this->addSql('ALTER TABLE coupon DROP discount, CHANGE addedby verifiedBy INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coupon ADD CONSTRAINT FK_64BF3F021C4DBF30 FOREIGN KEY (verifiedBy) REFERENCES operator (id)');
        $this->addSql('CREATE INDEX IDX_64BF3F021C4DBF30 ON coupon (verifiedBy)');
    }
}
