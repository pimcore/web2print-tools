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


namespace Web2PrintToolsBundle;

use Pimcore\Logger;

class FavoriteOutputDefinition extends \Pimcore\Model\AbstractModel {
    public $id;
    public $o_classId;
    public $description;
    public $configuration;


    public static function getById($id) {
        try {
            $config = new self();
            $config->getDao()->getById($id);
            return $config;
        } catch(\Exception $ex) {
            Logger::debug($ex->getMessage());
            return null;
        }        
    }

    /**
     * @param array $values
     * @return FavoriteOutputDefinition
     */
    public static function create($values = array()) {
        $config = new self();
        $config->setValues($values);
        return $config;
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


    public function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }

    public function getConfiguration() {
        return $this->configuration;
    }

    public function setO_ClassId($o_classId) {
        $this->o_classId = $o_classId;
    }

    public function getO_ClassId() {
        return $this->o_classId;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

}
