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


namespace Web2PrintToolsBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Web2PrintToolsBundle\FavoriteOutputDefinition;

/**
 * Class AdminController
 * @Route("/admin")
 */
class AdminController extends \Pimcore\Bundle\AdminBundle\Controller\AdminController {

    /**
     * @param Request $request
     * @Route("/favorite-output-definitions-table-proxy")
     */
    public function favoriteOutputDefinitionsTableProxyAction(Request $request) {

        if ($request->get("data")) {
            if ($request->get("xaction") == "destroy") {
                $id = json_decode($request->get("data"), true);
                $def = FavoriteOutputDefinition::getById($id['id']);
                if(!empty($def)) {
                    $def->delete();
                    return $this->adminJson(array("data" => array(), "success" => true));
                } else {
                    throw new \Exception("OutputDefinition with id " . $id . " not found.");
                }
            }
            else if ($request->get("xaction") == "update") {

                $data = json_decode($request->get("data"), true);
                $def = FavoriteOutputDefinition::getById($data['id']);
                if(!empty($def)) {
                    $def->setValues($data);
                    $def->save();
                    return $this->adminJson(array("data" => get_object_vars($def), "success" => true));
                } else {
                    throw new \Exception("Definition with id " . $data['id'] . " not found.");
                }
            } else if ($request->get("xaction") == "create") {
                $data = json_decode($request->get("data"), true);
                unset($data['id']);
                $def = new FavoriteOutputDefinition();
                $def->setValues($data);
                $def->save();
                return $this->adminJson(array("data" => get_object_vars($def), "success" => true));
            }
        } else {

            $list = new FavoriteOutputDefinition\Listing();
            $list->setOrder("asc");
            $list->setOrderKey("description");

            if($request->get("sort")) {
                $sortConfig = json_decode($request->get("sort"), true);
                $sortConfig = $sortConfig[0];
                if($sortConfig->property) {
                    $list->setOrderKey($sortConfig->property);
                }
                if($sortConfig->direction) {
                    $list->setOrder($sortConfig->direction);
                }
            }

            $list->setLimit($request->get("limit"));
            $list->setOffset($request->get("start"));

            $condition = "1 = 1";
            if($request->get("filter")) {
                $filterString = $request->get("filter");
                $filters = json_decode($filterString, true);

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

            return $this->adminJson(array("data" => $definitions, "success" => true, "total" => $list->getTotalCount()));
        }
    }

    /**
     * @param Request $reques
     * @Route("/favorite-output-definitions")
     */
    public function favoriteOutputDefinitionsAction(Request $request) {
        $list = new FavoriteOutputDefinition\Listing();
        $list->setOrder("asc");
        $list->setOrderKey("description");
        $condition = "o_classId = " . $request->get("classId");
        $list->setCondition($condition);

        $definitions = array();
        foreach ($list->getOutputDefinitions() as $u) {
            $definitions[] = get_object_vars($u);
        }

        return $this->adminJson(array("data" => $definitions, "success" => true, "total" => $list->getTotalCount()));
    }

    /**
     * @param Request $request
     * @Route("/save-or-update-favorite-output-definition")
     */
    public function saveOrUpdateFavoriteOutputDefinitionAction(Request $request) {
        $configuration = $request->get("configuration");
        $id = $request->get("existing");
        $newName = strip_tags($request->get("text"));
        $savedConfig = FavoriteOutputDefinition::getById($id);

        if($id && $savedConfig) {
            $savedConfig->setConfiguration($configuration);
            $savedConfig->save();
            return $this->adminJson(array("success" => true));
        } else if($newName) {

            $list = new FavoriteOutputDefinition\Listing();
            $classId = $request->get("classId");
            $list->setCondition("o_classId = " . $list->quote($classId) . " AND description = " . $list->quote($newName));
            $existingOnes = $list->load();
            if(!empty($existingOnes) && !$request->get("force")) {
                return $this->adminJson(array("success" => false, "nameexists" => true, "id" => $existingOnes[0]->getId()));
            } else {
                $newConfiguration = new FavoriteOutputDefinition();
                $newConfiguration->setO_ClassId($request->get("classId"));
                $newConfiguration->setDescription($newName);
                $newConfiguration->setConfiguration($configuration);
                $newConfiguration->save();
                return $this->adminJson(array("success" => true));
            }
        } else {
            return $this->adminJson(array("success" => false));
        }
    }

}
