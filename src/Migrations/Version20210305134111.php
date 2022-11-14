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

namespace Web2PrintToolsBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Config;
use Pimcore\Model\Tool\SettingsStore;
use Web2PrintToolsBundle\FavoriteOutputDefinition\Dao;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20210305134111 extends AbstractMigration
{
    public function doesSqlMigrations(): bool
    {
        return false;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $result = null;
        try {
            if (Config::getSystemConfiguration()) {
                $result = \Pimcore\Db::get()->fetchAssociative("SHOW TABLES LIKE '" . Dao::TABLE_NAME . "';");
            }
        } catch (\Exception $e) {
        }
        $installed = !empty($result);

        SettingsStore::set('BUNDLE_INSTALLED__Web2PrintToolsBundle\\Web2PrintToolsBundle', $installed, 'bool', 'pimcore');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
    }
}
