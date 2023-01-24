<?php

declare(strict_types=1);

namespace Web2PrintToolsBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Web2PrintToolsBundle\FavoriteOutputDefinition\Dao;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230124103907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migrate columns with o_ prefix';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable(Dao::TABLE_NAME);

        $this->addSql(sprintf(
            'ALTER TABLE `%s` RENAME COLUMN `%s` TO `%s`',
            $table->getName(),
            'o_classId',
            'classId'
        ));
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('Dao::TABLE_NAME');

        $this->addSql(sprintf(
            'ALTER TABLE `%s` RENAME COLUMN `%s` TO `%s`',
            $table->getName(),
            'classId',
            'o_classId'
        ));
    }
}
