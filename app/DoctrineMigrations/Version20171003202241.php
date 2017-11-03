<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171003202241 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE `article` SET `id` = 1,`header` = \'Bourdaloue Tart (Pear Tart)\',`url` = 0x2f726563697065732f626f757264616c6f75652d746172742f706561722d746172742e68746d6c,`body` = \'<p><img alt=\"\" src=\"/articles/images/recipes/bourdaloue-tart/img1.png\" style=\"height:224px; width:200px\" /><img alt=\"\" src=\"/articles/images/recipes/bourdaloue-tart/img2.png\" style=\"height:153px; width:135px\" /><img alt=\"\" src=\"/articles/images/recipes/bourdaloue-tart/img3.png\" style=\"height:153px; width:135px\" /><img alt=\"\" src=\"/articles/images/recipes/bourdaloue-tart/img4.png\" style=\"height:153px; width:136px\" /></p>\r\n\r\n<p>The celebrated p&acirc;tissier Coquelin bought La P&acirc;tisserie Bourdaloue (which is still up and baking on the rue Bourdaloue) in 1909, and created, among other things, this famous pear tart with frangipane / almond cream. Certain desserts stand the test of time and one of those classics is la tart bourdaloue. It has remained one of my favorites, both to make and to eat.</p>\r\n\r\n<h2>Ingridients</h2>\r\n\r\n<table align=\"center\" border=\"0\" cellpadding=\"1\" cellspacing=\"1\">\r\n  <thead>\r\n     <tr>\r\n            <th colspan=\"2\" rowspan=\"1\" scope=\"col\">\r\n          <p>Almond Sugar Dough</p>\r\n\r\n           <p>(Pate sucree amande)</p>\r\n         </th>\r\n           <th colspan=\"2\" rowspan=\"1\" scope=\"col\">\r\n          <p>Almond Cream/Frangipane</p>\r\n\r\n          <p>(Creme d&rsquo; amande/ frangipane)</p>\r\n          </th>\r\n       </tr>\r\n   </thead>\r\n    <tbody>\r\n     <tr>\r\n            <td>Butter</td>\r\n         <td>265 g</td>\r\n          <td>Butter</td>\r\n         <td>200 g</td>\r\n      </tr>\r\n       <tr>\r\n            <td>Powdered sugar</td>\r\n         <td>200 g</td>\r\n          <td>Powdered sugar</td>\r\n         <td>200 g</td>\r\n      </tr>\r\n       <tr>\r\n            <td>Almond powder</td>\r\n          <td>75g</td>\r\n            <td>Almond powder</td>\r\n          <td>200 g</td>\r\n      </tr>\r\n       <tr>\r\n            <td>Salt</td>\r\n           <td>3 g</td>\r\n            <td>Corn starch</td>\r\n            <td>30 g</td>\r\n       </tr>\r\n       <tr>\r\n            <td>Eggs</td>\r\n           <td>100 g</td>\r\n          <td>Eggs</td>\r\n           <td>200 g</td>\r\n      </tr>\r\n       <tr>\r\n            <td>All purpose flour</td>\r\n          <td>500 g</td>\r\n          <td>Rum</td>\r\n            <td>15 g</td>\r\n       </tr>\r\n       <tr>\r\n            <td>Vanilla powder</td>\r\n         <td>teaspoon</td>\r\n           <td>Pastry cream</td>\r\n           <td>250g</td>\r\n       </tr>\r\n   </tbody>\r\n</table>\r\n\r\n<h2>Directions</h2>\r\n\r\n<p><img alt=\"\" src=\"/articles/images/recipes/bourdaloue-tart/img6.png\" style=\"height:173px; width:241px\" /></p>\r\n\r\n<p><span style=\"font-size:16px\"><strong>Work the butter, sugar, almond powder and salt in the mixer. Add the eggs and the flour at the end. Let rest for two hours before use.</strong></span></p>\r\n\r\n<p><span style=\"font-size:16px\"><strong>Soften the butter with the sugar and the almond powder. Add the starch, the eggs and the Rum. Fold in the smooth pastry cream before use.va</strong></span></p>\r\n\r\n<p><span style=\"font-size:16px\"><strong>Frangipane Proportion: 500g pastry cream &amp; 1,000g almond cream</strong></span></p>\r\n\r\n<p>Roll out the dough to about a 3mm thickness and then use a &ldquo;docker&rdquo; to make dots in it. then put that dough in a mold (you can use either the 18-20 cm diameter mold or the one in 8 cm diameter, with a height of 1.5 cm). Once you have put the dough in a mold you have created a &ldquo;shell&rdquo;. Now pipe the frangipane in a spiral manner inside the shell but&nbsp;<strong>NOT</strong>&nbsp;all the way up so you have space to put something on top. Make sure that the mold is of top of a black silpat (used to make bread) and now you can strain the canned pears, slice them in a &ldquo;fancy&rdquo; manner and put them on top. Make sure not to push on the frangipane and then bake at 350 degrees Fahrenheit for 35 min. Glaze in optional on top but it just makes it shiny and doesn&#39;t have an effect on the taste, i personally don&#39;t recommend it because i want to stay as healthy as possible.</p>\',`is_homepage` = 1 WHERE `article`.`id` = 1');
        $this->addSql('INSERT INTO `menu` (`id`, `name`, `header`) VALUES (2, \'sidebar\', \'Top-rated articles\')');
        $this->addSql('UPDATE `menu_item` SET `position` = 1 WHERE `menu_item`.`id` = 1');
        $this->addSql('UPDATE `menu_item` SET `position` = 2 WHERE `menu_item`.`id` = 2');
        $this->addSql('INSERT INTO `menu_item` (`id`, `menu_id`, `url`, `title`, `position`) VALUES
            (3, 2, 0x2f626573742d63616e646965732d66726f6d2d7468652d776f726c642e68746d6c, \'20 Amazing international candies\', 0),
            (4, 2, 0x2f626573742d636f6f6b696e672d63616d70732d666f722d6b6964732e68746d6c, \'Best Cooking Camps for Kids\', 1),
            (5, 2, 0x2f63686f636f6c6174652d6d697874757265732d666f722d7468652d61697262727573682d67756e2e68746d6c, \'Chocolate Mixtures For the Airbrush Gun\', 2),
            (6, 2, 0x2f66616b652d63616b652e68746d6c, \'Fake Cake\', 3),
            (7, 2, 0x2f6e616b65642d77656464696e672d63616b652e68746d6c, \'Naked Wedding Cake\', 4),
            (8, 2, 0x2f7468652d31302d626573742d63686f636f6c6174696572732d696e2d7468652d776f726c642e68746d6c, \'The 10 Best Chocolatiers in the World\', 5),
            (9, 1, 0x2f, \'Back to: BakingShop.com\', 0)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs 
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE `menu_item` SET `position` = 0 WHERE `menu_item`.`id` = 1');
        $this->addSql('UPDATE `menu_item` SET `position` = 1 WHERE `menu_item`.`id` = 2');
        $this->addSql('DELETE FROM `menu_item` WHERE `menu_item`.`id` in (3, 4, 5, 6, 7, 8, 9)');
        $this->addSql('DELETE FROM `menu` WHERE `menu`.`id` = 2');

    }
}
