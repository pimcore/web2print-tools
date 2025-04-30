<?php

declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Web2PrintToolsBundle\Migrations\PimcoreX;

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

        if ($table->hasColumn('o_classId')) {
            $this->addSql(sprintf(
                'ALTER TABLE `%s` CHANGE COLUMN `%s` `%s` varchar(50) NULL',
                $table->getName(),
                'o_classId',
                'classId'
            ));
        }
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable(Dao::TABLE_NAME);

        if ($table->hasColumn('classId')) {
            $this->addSql(sprintf(
                'ALTER TABLE `%s` CHANGE COLUMN `%s` `%s` varchar(50) NULL',
                $table->getName(),
                'classId',
                'o_classId'
            ));
        }
    }
}
