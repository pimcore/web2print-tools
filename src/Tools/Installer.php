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

namespace Web2PrintToolsBundle\Tools;

use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Web2PrintToolsBundle\FavoriteOutputDefinition\Dao;
use Web2PrintToolsBundle\Migrations\PimcoreX\Version20230124103907;

class Installer extends SettingsStoreAwareInstaller
{
    public function install(): void
    {
        $db = \Pimcore\Db::get();
        $db->executeQuery("INSERT IGNORE INTO users_permission_definitions(`key`) VALUES ('web2print_web2print_favourite_output_channels')");

        $db->executeQuery('
            CREATE TABLE IF NOT EXISTS `' . Dao::TABLE_NAME . '` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `classId` varchar(50) NULL,
              `description` varchar(255) COLLATE utf8_bin NULL,
              `configuration` longtext CHARACTER SET latin1,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
        ');

        parent::install();
    }

    public function needsReloadAfterInstall(): bool
    {
        return true;
    }

    public function uninstall(): void
    {
        $db = \Pimcore\Db::get();
        $db->executeQuery('DROP TABLE IF EXISTS `' . Dao::TABLE_NAME . '`');

        parent::uninstall();

        if (self::isInstalled()) {
            throw new InstallationException('Could not be uninstalled.');
        }
    }

    public function getLastMigrationVersionClassName(): ?string
    {
        return Version20230124103907::class;
    }
}
