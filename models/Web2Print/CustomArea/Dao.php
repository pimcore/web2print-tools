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


namespace Web2Print\CustomArea;

use Web2Print\CustomArea;

class Dao extends \Pimcore\Model\Dao\AbstractDao {

    const TABLE_NAME = "plugin_web2print_custom_area";

    /**
     * Contains all valid columns in the database table
     *
     * @var array
     */
    protected $validColumns = array();

    /**
     * Get the valid columns from the database
     *
     * @return void
     */
    public function init() {
        $this->validColumns = $this->getValidTableColumns(self::TABLE_NAME);
    }


    /**
     * @return void
     */
    public function getById($id) {
        $outputDefinitionRaw = $this->db->fetchRow("SELECT * FROM " . self::TABLE_NAME . " WHERE id=?", array($id));
        if(empty($outputDefinitionRaw)) {
            throw new \Exception("OutputDefinition-Id " . $id . " not found.");
        }
        $this->assignVariablesToModel($outputDefinitionRaw);
    }


    /**
     * Create a new record for the object in database
     *
     * @return boolean
     */
    public function create() {
        $this->db->insert(self::TABLE_NAME, array());
        $this->model->setId($this->db->lastInsertId());

        $this->save();
    }

    /**
     * Save object to database
     *
     * @return void
     */
    public function save() {
        if ($this->model->getId()) {
            return $this->update();
        }
        return $this->create();
    }

    /**
     * @return void
     */
    public function update() {

        $class = get_object_vars($this->model);

        foreach ($class as $key => $value) {
            if (in_array($key, $this->validColumns)) {

                if (is_array($value) || is_object($value)) {
                    $value = serialize($value);
                } else  if(is_bool($value)) {
                    $value = (int)$value;
                }
                $data[$key] = $value;
            }
        }
        $this->db->update(self::TABLE_NAME, $data, "id=" . $this->db->quote($this->model->getId()));

        $this->createOrUpdateAreaFiles();

    }

    /**
     * Deletes object from database
     *
     * @return void
     */
    public function delete() {
        $this->db->delete(self::TABLE_NAME, "id=" . $this->db->quote($this->model->getId()));

        $this->deleteAreaFiles(CustomArea::CUSTOM_AREA_PREFIX . $this->model->getId());
    }



    private function createOrUpdateAreaFiles() {

        $areaName = CustomArea::CUSTOM_AREA_PREFIX . $this->model->getId();

        if(!(is_dir(PIMCORE_WEBSITE_PATH . "/var/areas/" . $areaName))) {
            mkdir(PIMCORE_WEBSITE_PATH . "/var/areas/" . $areaName);
        }
        $this->createFile($areaName, "action.php");
        $this->createFile($areaName, "area.xml");
        $this->createFile($areaName, "editmode.css");
        $this->createFile($areaName, "editmode.php");
        $this->createFile($areaName, "frontend.css");
        $this->createFile($areaName, "frontend.php");
        $this->createFile($areaName, "view.php");

        $this->replaceInFile($areaName, "action.php", "/Document_Tag_Area_web2print_customarea_template/", "Document_Tag_Area_web2print_customarea_" . $this->model->getId());
        $this->replaceInFile($areaName, "area.xml", "/<id>(.*)<\/id>/", "<id>" . CustomArea::CUSTOM_AREA_PREFIX . $this->model->getId() . "</id>");
        $this->replaceInFile($areaName, "area.xml", "/<name>(.*)<\/name>/", "<name>" . $this->model->getName() . "</name>");
        $this->replaceInFile($areaName, "area.xml", "/<classId>(.*)<\/classId>/", "<classId>" . $this->model->getClassId() . "</classId>");


        if($this->model->getActive()) {
            \Pimcore\ExtensionManager::enable("brick", $areaName);
        } else {
            \Pimcore\ExtensionManager::disable("brick", $areaName);
        }


    }

    private function createFile($areaName, $filename) {
        if(!(is_file(PIMCORE_WEBSITE_PATH . "/var/areas/" . $areaName . "/" . $filename))) {
            copy(PIMCORE_PLUGINS_PATH . "/Web2Print/install/web2print_customarea_template/" . $filename, PIMCORE_WEBSITE_PATH . "/var/areas/" . $areaName . "/" . $filename);
        }
    }

    private function replaceInFile($areaName, $filename, $search, $replace) {
        $fullFilename = PIMCORE_WEBSITE_PATH . "/var/areas/" . $areaName . "/" . $filename;
        $fileContents = file_get_contents($fullFilename);
        $fileContents = preg_replace($search, $replace, $fileContents);
        file_put_contents($fullFilename, $fileContents);
    }

    private function deleteAreaFiles($areaName) {
        $path = PIMCORE_WEBSITE_PATH . "/var/areas/" . $areaName;
        $sourceFiles = scandir($path);
        foreach ($sourceFiles as $filename) {
            unlink($path . "/" . $filename);
        }

        rmdir($path);
    }

}
