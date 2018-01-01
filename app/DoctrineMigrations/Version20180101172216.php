<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180101172216 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO `operator` (`name`) VALUES (\'Samanta\')');
        $this->addSql('INSERT INTO `operator` (`name`) VALUES (\'John\')');
        $this->addSql('INSERT INTO `operator` (`name`) VALUES (\'Emma\')');
        $this->addSql('INSERT INTO `operator` (`name`) VALUES (\'Eddie\')');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM `operator` WHERE `operator`.`name` = \'Samanta\'');
        $this->addSql('DELETE FROM `operator` WHERE `operator`.`name` = \'John\'');
        $this->addSql('DELETE FROM `operator` WHERE `operator`.`name` = \'Emma\'');
        $this->addSql('DELETE FROM `operator` WHERE `operator`.`name` = \'Eddie\'');
    }
}
