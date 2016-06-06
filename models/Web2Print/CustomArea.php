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


use Elements\OutputDataConfigToolkit\OutputDefinition;
use Web2Print\CustomArea\Listing;

class CustomArea extends \Pimcore\Model\AbstractModel {
    CONST CUSTOM_AREA_PREFIX = "web2print_customarea_";

    public $id;
    public $name;
    public $description;
    public $active;
    public $classId;
    public $type;
    public $selectedFavouriteOutputChannel;

    public static function getById($id) {
        try {
            $config = new self();
            $config->getDao()->getById($id);
            return $config;
        } catch(\Exception $ex) {
            \Logger::debug($ex->getMessage());
            return null;
        }        
    }

    /**
     * @param array $values
     * @return OutputDefinition
     */
    public static function create($values = array()) {
        $config = new self();
        $config->setValues($values);
        return $config;
    }

    public static function getCustomAreaIdsByType($type) {
        $customAreaList = new Listing();
        $db = \Pimcore\Db::get();
        $customAreaList->setCondition("type = " . $db->quote($type));
        $customAreaList = $customAreaList->getCustomAreas();

        $names = array();
        foreach($customAreaList as $area) {
            $names[] = $area->getCustomAreaId();
        }

        return $names;
    }

    /**
     * @return void
     */
    public function save() {
        $this->getDao()->save();
    }

    /**
     * @return void
     */
    public function delete() {
        $this->getDao()->delete();
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setClassId($classId)
    {
        $this->classId = $classId;
    }

    public function getClassId()
    {
        return $this->classId;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setSelectedFavouriteOutputChannel($selectedFavouriteOutputChannel)
    {
        $this->selectedFavouriteOutputChannel = $selectedFavouriteOutputChannel;
    }

    public function getSelectedFavouriteOutputChannel()
    {
        return $this->selectedFavouriteOutputChannel;
    }


    public function getCustomAreaId() {
        return self::CUSTOM_AREA_PREFIX . $this->getId();
    }

}
