<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171005121853 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE `article` SET `type` = \'info\' WHERE `article`.`id` in (2, 3)');
        $this->addSql('UPDATE `article` SET `type` = \'recipe\' WHERE `article`.`id` = 1');
        $this->addSql('UPDATE `article` SET `type` = \'article\' WHERE `article`.`id` in (4, 5, 6, 7, 8, 9)');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE `article` SET `type` = null WHERE `article`.`id` in (1, 2, 3, 4, 5, 6, 7, 8, 9)');

    }
}
