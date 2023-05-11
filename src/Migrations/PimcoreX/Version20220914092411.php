<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Web2PrintToolsBundle\Migrations\PimcoreX;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Db;
use Pimcore\Migrations\BundleAwareMigration;
use Web2PrintToolsBundle\FavoriteOutputDefinition\Dao;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20220914092411 extends BundleAwareMigration
{
    protected function getBundleName(): string
    {
        return 'Web2PrintToolsBundle';
    }

    public function up(Schema $schema): void
    {
        $db = Db::get();

        if ($column = $this->getClassIdColumn($schema)) {
            $this->addSql('alter table ' . $db->quoteIdentifier(DAO::TABLE_NAME) . ' modify ' . $column .' varchar(50) null');
        }
        $this->addSql('alter table ' . $db->quoteIdentifier(DAO::TABLE_NAME) . ' modify ' . $db->quoteIdentifier('description') . ' varchar(255) null');
    }

    public function down(Schema $schema): void
    {
        $db = Db::get();
        if ($column = $this->getClassIdColumn($schema)) {
            $this->addSql('alter table ' . $db->quoteIdentifier(DAO::TABLE_NAME) . ' modify ' . $column .' varchar(50) not null');
        }
        $this->addSql('alter table ' . $db->quoteIdentifier(DAO::TABLE_NAME) . ' modify ' . $db->quoteIdentifier('description') . ' varchar(255) not null;');
    }

    private function getClassIdColumn(Schema $schema): ?string
    {
        $table = $schema->getTable(DAO::TABLE_NAME);
        if ($table->hasColumn('o_classid')) {
            return 'o_classid';
        } elseif ($table->hasColumn('classId')) {
            return 'classId';
        }

        return null;
    }
}
