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


class Web2Print_AdminController extends \Pimcore\Controller\Action\Admin {

    public function favoriteOutputDefinitionsTableProxyAction() {

        if ($this->getParam("data")) {
            if ($this->getParam("xaction") == "destroy") {
                $id = Zend_Json::decode($this->getParam("data"));
                $def = \Web2Print\FavoriteOutputDefinition::getById($id['id']);
                if(!empty($def)) {
                    $def->delete();
                    $this->_helper->json(array("data" => array(), "success" => true));
                } else {
                    throw new Exception("OutputDefinition with id " . $id . " not found.");
                }
            }
            else if ($this->getParam("xaction") == "update") {

                $data = Zend_Json::decode($this->getParam("data"));
                $def = \Web2Print\FavoriteOutputDefinition::getById($data['id']);
                if(!empty($def)) {
                    $def->setValues($data);
                    $def->save();
                    $this->_helper->json(array("data" => get_object_vars($def), "success" => true));
                } else {
                    throw new Exception("Definition with id " . $data['id'] . " not found.");
                }
            } else if ($this->getParam("xaction") == "create") {
                $data = Zend_Json::decode($this->getParam("data"));
                unset($data['id']);
                $def = new \Web2Print\FavoriteOutputDefinition();
                $def->setValues($data);
                $def->save();
                $this->_helper->json(array("data" => get_object_vars($def), "success" => true));
            }
        } else {

            $list = new \Web2Print\FavoriteOutputDefinition\Listing();
            $list->setOrder("asc");
            $list->setOrderKey("description");

            if($this->getParam("sort")) {
                $sortConfig = json_decode($this->getParam("sort"));
                $sortConfig = $sortConfig[0];
                if($sortConfig->property) {
                    $list->setOrderKey($sortConfig->property);
                }
                if($sortConfig->direction) {
                    $list->setOrder($sortConfig->direction);
                }
            }

            $list->setLimit($this->getParam("limit"));
            $list->setOffset($this->getParam("start"));

            $condition = "1 = 1";
            if($this->getParam("filter")) {
                $filterString = $this->getParam("filter");
                $filters = json_decode($filterString);
                
                $db = \Pimcore\Db::get();
                foreach($filters as $f) {

                    if($f->type == "string") {
                        $condition .= " AND " . $db->quoteIdentifier($f->property) . " LIKE " . $db->quote("%" . $f->value . "%");
                    }
                }
                $list->setCondition($condition);
            }
            $list->load();

            $definitions = array();
            foreach ($list->getOutputDefinitions() as $u) {
                $definitions[] = get_object_vars($u);
            }

            $this->_helper->json(array("data" => $definitions, "success" => true, "total" => $list->getTotalCount()));
        }


    }

    public function favoriteOutputDefinitionsAction() {
        $list = new \Web2Print\FavoriteOutputDefinition\Listing();
        $list->setOrder("asc");
        $list->setOrderKey("description");
        $condition = "o_classId = " . $this->getParam("classId");
        $list->setCondition($condition);

        $definitions = array();
        foreach ($list->getOutputDefinitions() as $u) {
            $definitions[] = get_object_vars($u);
        }

        $this->_helper->json(array("data" => $definitions, "success" => true, "total" => $list->getTotalCount()));
    }

    public function saveOrUpdateFavoriteOutputDefinitionAction() {
        $configuration = $this->getParam("configuration");
        $id = $this->getParam("existing");
        $newName = strip_tags($this->getParam("text"));
        $savedConfig = \Web2Print\FavoriteOutputDefinition::getById($id);

        if($id && $savedConfig) {
            $savedConfig->setConfiguration($configuration);
            $savedConfig->save();
            $this->_helper->json(array("success" => true));
        } else if($newName) {

            $list = new \Web2Print\FavoriteOutputDefinition\Listing();
            $classId = $this->getParam("classId");
            $list->setCondition("o_classId = " . $list->quote($classId) . " AND description = " . $list->quote($newName));
            $existingOnes = $list->load();
            if(!empty($existingOnes) && !$this->getParam("force")) {
                $this->_helper->json(array("success" => false, "nameexists" => true, "id" => $existingOnes[0]->getId()));
            } else {
                $newConfiguration = new \Web2Print\FavoriteOutputDefinition();
                $newConfiguration->setO_ClassId($this->getParam("classId"));
                $newConfiguration->setDescription($newName);
                $newConfiguration->setConfiguration($configuration);
                $newConfiguration->save();
                $this->_helper->json(array("success" => true));
            }
        } else {
            $this->_helper->json(array("success" => false));
        }
    }

}