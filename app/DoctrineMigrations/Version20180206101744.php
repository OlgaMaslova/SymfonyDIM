<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;


class Version20180206101744 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `show` ADD category_id INT DEFAULT NULL, DROP category');
        $this->addSql('ALTER TABLE `show` ADD CONSTRAINT FK_320ED90112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_320ED90112469DE2 ON `show` (category_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `show` DROP FOREIGN KEY FK_320ED90112469DE2');
        $this->addSql('DROP INDEX IDX_320ED90112469DE2 ON `show`');
        $this->addSql('ALTER TABLE `show` ADD category VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP category_id');
    }
}
