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
 * @category   Pimcore
 * @package    EcommerceFramework
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Web2Print;

use \Pimcore\Model\Document;

class Plugin extends \Pimcore\API\Plugin\AbstractPlugin implements \Pimcore\API\Plugin\PluginInterface {

    /**
     * @return string $jsClassName
     */
    public static function getJsClassName(){
        return "pimcore.plugin.web2print";
    }

    /**
     * absolute path to the folder holding plugin translation files
     * @static
     * @return string
     */
    public static function getTranslationFileDirectory() {
        return PIMCORE_PLUGINS_PATH."/Web2Print/texts";
    }

    /**
    *
    * @param string $language
    * @return string path to the translation file relative to plugin direcory
    */
	public static function getTranslationFile($language){
        if($language == "de"){
            return "/Web2Print/texts/de.csv";
        } else if($language == "en"){
            return "/Web2Print/texts/en.csv";
        } else {
            return null;
        }
    }

    /**
     * @return string $statusMessage
     */
    public static function install() {
        $db = \Pimcore\Resource::get();

        $areasDir = PIMCORE_WEBSITE_PATH . "/views/areas";
        if(!file_exists($areasDir)) {
            mkdir($areasDir, 0755, true);
        }
        self::xcopy(PIMCORE_PLUGINS_PATH . "/Web2Print/frontend-samples/areas", $areasDir);

        $layoutsDir = PIMCORE_WEBSITE_PATH . "/views/layouts";
        if(!file_exists($layoutsDir)) {
            mkdir($layoutsDir, 0755, true);
        }
        self::xcopy(PIMCORE_PLUGINS_PATH . "/Web2Print/frontend-samples/layout", $layoutsDir);

        $staticDir = PIMCORE_WEBSITE_PATH . "/static/css";
        if(!file_exists($staticDir)) {
            mkdir($staticDir, 0755, true);
        }
        self::xcopy(PIMCORE_PLUGINS_PATH . "/Web2Print/frontend-samples/css", $staticDir);


        $db->query("INSERT IGNORE INTO users_permission_definitions(`key`) VALUES ('plugin_web2print_custom_area'), ('plugin_web2print_favourite_output_channels')");
        $db->query("
            CREATE TABLE IF NOT EXISTS `plugin_web2print_custom_area` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `description` text CHARACTER SET latin1,
                  `active` tinyint(1) DEFAULT NULL,
                  `classId` bigint(20) NOT NULL,
                  `type` varchar(20) DEFAULT NULL,
                  `selectedFavouriteOutputChannel` varchar(100) DEFAULT NULL,
                  PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $db->query("
            CREATE TABLE IF NOT EXISTS `plugin_web2print_favorite_outputdefinitions` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `o_classId` int(11) NOT NULL,
              `description` varchar(255) COLLATE utf8_bin NOT NULL,
              `configuration` longtext CHARACTER SET latin1,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
        ");

    }

    /**
     * @return boolean $isInstalled
     */
    public static function isInstalled() {
        $result = null;
        try{
            if(\Pimcore\Config::getSystemConfig()) {
                $result = \Pimcore\Db::get()->describeTable("plugin_web2print_favorite_outputdefinitions");
            }
        } catch(\Exception $e){}
        return !empty($result);
    }

    /**
     * @return boolean $needsReloadAfterInstall
     */
    public static function needsReloadAfterInstall() {
        return true;
    }

    /**
     * @return boolean $readyForInstall
     */
    public static function readyForInstall() {
        return !self::isInstalled();
    }

    /**
     * @return string $statusMessage
     */
    public static function uninstall() {
        \Pimcore\Db::get()->query("DROP TABLE IF EXISTS `plugin_web2print_favorite_outputdefinitions`");
    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @param       string   $permissions New folder creation permissions
     * @return      bool     Returns true on success, false on failure
     */
    public static function xcopy($source, $dest, $permissions = 0755)
    {
        if(file_exists($dest)) {
            \Logger::warn("Destination $dest exists, cancel copy.");
            return false;
        }

        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest, $permissions);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            self::xcopy("$source/$entry", "$dest/$entry");
        }

        // Clean up
        $dir->close();
        return true;
    }

}

