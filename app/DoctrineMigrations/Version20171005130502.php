<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171005130502 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE `menu` SET `name` = \'sidebar-articles\' WHERE `menu`.`id` = 2');
        $this->addSql('INSERT INTO `menu` (`id`, `name`, `header`) VALUES (3, \'sidebar-recipies\', \'Top-rated recipies\')');
        $this->addSql('INSERT INTO `menu_item` (`id`, `menu_id`, `url`, `title`, `position`) VALUES
            (10, 3, 0x2f726563697065732f626f757264616c6f75652d746172742f706561722d746172742e68746d6c, \'Bourdaloue Tart (Pear Tart)\', 0)');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE `menu` SET `name` = \'sidebar\' WHERE `menu`.`id` = 2');
        $this->addSql('DELETE FROM `menu_item` WHERE `menu_item`.`id` = 10');
        $this->addSql('DELETE FROM `menu` WHERE `menu`.`id` = 3');


    }
}
