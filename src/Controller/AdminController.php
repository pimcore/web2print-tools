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

namespace Web2PrintToolsBundle\Controller;

use Pimcore\Controller\Traits\JsonHelperTrait;
use Pimcore\Controller\UserAwareController;
use Pimcore\Db;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Web2PrintToolsBundle\FavoriteOutputDefinition;

/**
 * Class AdminController
 *
 * @Route("/admin")
 */
class AdminController extends UserAwareController
{
    use JsonHelperTrait;

    /**
     * @param Request $request
     * @Route("/favorite-output-definitions-table-proxy")
     */
    public function favoriteOutputDefinitionsTableProxyAction(Request $request)
    {
        if ($request->get('data')) {
            if ($request->get('xaction') == 'destroy') {
                $id = json_decode($request->get('data'), true);
                $idValue = $id['id'] ?? '';
                if (!empty($idValue)) {
                    $def = FavoriteOutputDefinition::getById($idValue);
                    if (!empty($def)) {
                        $def->delete();

                        return $this->jsonResponse(['data' => [], 'success' => true]);
                    }
                }
                throw new \Exception('OutputDefinition with id ' . $idValue . ' not found.');
            } elseif ($request->get('xaction') == 'update') {
                $data = json_decode($request->get('data'), true);
                $def = FavoriteOutputDefinition::getById($data['id']);
                if (!empty($def)) {
                    $def->setValues($data);
                    $def->save();

                    return $this->jsonResponse(['data' => get_object_vars($def), 'success' => true]);
                } else {
                    throw new \Exception('Definition with id ' . $data['id'] . ' not found.');
                }
            } elseif ($request->get('xaction') == 'create') {
                $data = json_decode($request->get('data'), true);
                unset($data['id']);
                $def = new FavoriteOutputDefinition();
                $def->setValues($data);
                $def->save();

                return $this->jsonResponse(['data' => get_object_vars($def), 'success' => true]);
            }
        } else {
            $list = new FavoriteOutputDefinition\Listing();
            $list->setOrder('asc');
            $list->setOrderKey('description');

            if ($request->get('sort')) {
                $sortConfig = json_decode($request->get('sort'), true);
                $sortConfig = $sortConfig[0];
                if ($sortConfig['property']) {
                    $list->setOrderKey($sortConfig['property']);
                }
                if ($sortConfig['direction']) {
                    $list->setOrder($sortConfig['direction']);
                }
            }

            $list->setLimit($request->get('limit'));
            $list->setOffset($request->get('start'));

            $condition = '1 = 1';
            if ($request->get('filter')) {
                $filterString = $request->get('filter');
                $filters = json_decode($filterString, true);

                $db = \Pimcore\Db::get();
                foreach ($filters as $f) {
                    if ($f->type == 'string') {
                        $condition .= ' AND ' . $db->quoteIdentifier($f->property) . ' LIKE ' . $db->quote('%' . $f->value . '%');
                    }
                }
                $list->setCondition($condition);
            }
            $list->load();

            $definitions = [];
            foreach ($list->getOutputDefinitions() as $u) {
                $definitions[] = get_object_vars($u);
            }

            return $this->jsonResponse(['data' => $definitions, 'success' => true, 'total' => $list->getTotalCount()]);
        }
    }

    /**
     * @param Request $request
     * @Route("/favorite-output-definitions")
     */
    public function favoriteOutputDefinitionsAction(Request $request)
    {
        $list = new FavoriteOutputDefinition\Listing();
        $list->setOrder('asc');
        $list->setOrderKey('description');
        $condition = (DataObject\Service::getVersionDependentDatabaseColumnName('classId') .' = ' . $list->quote($request->get('classId')));
        $list->setCondition($condition);

        $definitions = [];
        foreach ($list->getOutputDefinitions() as $u) {
            $definitions[] = get_object_vars($u);
        }

        return $this->jsonResponse(['data' => $definitions, 'success' => true, 'total' => $list->getTotalCount()]);
    }

    /**
     * @param Request $request
     * @Route("/save-or-update-favorite-output-definition")
     */
    public function saveOrUpdateFavoriteOutputDefinitionAction(Request $request)
    {
        $configuration = $request->get('configuration');
        $id = $request->get('existing');
        $newName = strip_tags($request->get('text'));
        $savedConfig = FavoriteOutputDefinition::getById($id);

        if ($id && $savedConfig) {
            $savedConfig->setConfiguration($configuration);
            $savedConfig->save();

            return $this->jsonResponse(['success' => true]);
        } elseif ($newName) {
            $db = Db::get();
            $list = new FavoriteOutputDefinition\Listing();
            $classId = $request->get('classId');
            $list->setCondition(DataObject\Service::getVersionDependentDatabaseColumnName('classId') .' = ' . $list->quote($classId) . ' AND ' . $db->quoteIdentifier('description') . ' = ' . $list->quote($newName));
            $existingOnes = $list->load();
            if (!empty($existingOnes) && !$request->get('force')) {
                return $this->jsonResponse(['success' => false, 'nameexists' => true, 'id' => $existingOnes[0]->getId()]);
            } else {
                $newConfiguration = new FavoriteOutputDefinition();
                $newConfiguration->setClassId($request->get('classId'));
                $newConfiguration->setDescription($newName);
                $newConfiguration->setConfiguration($configuration);
                $newConfiguration->save();

                return $this->jsonResponse(['success' => true]);
            }
        } else {
            return $this->jsonResponse(['success' => false]);
        }
    }
}
