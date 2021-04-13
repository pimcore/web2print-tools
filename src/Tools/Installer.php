<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


namespace Web2PrintToolsBundle\Tools;

use Pimcore\Config;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Web2PrintToolsBundle\FavoriteOutputDefinition\Dao;

class Installer extends SettingsStoreAwareInstaller {


    public function install()
    {
        $db = \Pimcore\Db::get();
        $db->query("INSERT IGNORE INTO users_permission_definitions(`key`) VALUES ('web2print_web2print_favourite_output_channels')");

        $db->query("
            CREATE TABLE IF NOT EXISTS `" . Dao::TABLE_NAME . "` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `o_classId` varchar(50) NOT NULL,
              `description` varchar(255) COLLATE utf8_bin NOT NULL,
              `configuration` longtext CHARACTER SET latin1,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
        ");

        parent::install();

        if($this->isInstalled()){
            return true;
        } else {
            return false;
        }

    }

    public function needsReloadAfterInstall()
    {
        return true;
    }

    public function uninstall()
    {
        $db = \Pimcore\Db::get();
        $db->query("DROP TABLE IF EXISTS `" . Dao::TABLE_NAME . "`");

        parent::uninstall();

        if(self::isInstalled()){
            throw new InstallationException("Could not be uninstalled.");
        }
    }

}
