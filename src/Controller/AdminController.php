<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Web2PrintToolsBundle\Controller;

use Exception;
use Pimcore\Controller\Traits\JsonHelperTrait;
use Pimcore\Controller\UserAwareController;
use Pimcore\Db;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Web2PrintToolsBundle\FavoriteOutputDefinition;

/**
 * Class AdminController
 */
#[Route('/admin')]
class AdminController extends UserAwareController
{
    use JsonHelperTrait;

    #[Route('/favorite-output-definitions-table-proxy')]
    public function favoriteOutputDefinitionsTableProxyAction(Request $request)
    {
        if ($request->request->getString('data')) {
            $data = json_decode($request->request->getString('data'), true);
            if ($request->query->getString('xaction') === 'destroy') {
                $idValue = $data['id'] ?? '';
                if (!empty($idValue)) {
                    $def = FavoriteOutputDefinition::getById($idValue);
                    if (!empty($def)) {
                        $def->delete();

                        return $this->jsonResponse(['data' => [], 'success' => true]);
                    }
                }

                throw new Exception('OutputDefinition with id ' . $idValue . ' not found.');
            } elseif ($request->query->getString('xaction') === 'update') {
                $def = FavoriteOutputDefinition::getById($data['id']);
                if (!empty($def)) {
                    $def->setValues($data);
                    $def->save();

                    return $this->jsonResponse(['data' => get_object_vars($def), 'success' => true]);
                }

                throw new Exception('Definition with id ' . $data['id'] . ' not found.');
            } elseif ($request->query->getString('xaction') === 'create') {
                unset($data['id']);
                $def = new FavoriteOutputDefinition();
                $def->setValues($data);
                $def->save();

                return $this->jsonResponse(['data' => get_object_vars($def), 'success' => true]);
            }
        }

        $list = new FavoriteOutputDefinition\Listing();
        $list->setOrder('asc');
        $list->setOrderKey('description');

        if ($request->request->getString('sort')) {
            $sortConfig = json_decode($request->request->getString('sort'), true);
            $sortConfig = $sortConfig[0];
            if ($sortConfig['property']) {
                $list->setOrderKey($sortConfig['property']);
            }
            if ($sortConfig['direction']) {
                $list->setOrder($sortConfig['direction']);
            }
        }

        $list->setLimit($request->request->getInt('limit'));
        $list->setOffset($request->request->getInt('start'));

        $condition = '1 = 1';
        if ($request->request->getString('filter')) {
            $filterString = $request->request->getString('filter');
            $filters = json_decode($filterString, true);

            $db = Db::get();

            foreach ($filters as $f) {
                if ($f['type'] === 'string') {
                    $condition .= ' AND ' . $db->quoteIdentifier($f['property']) . ' LIKE ' . $db->quote('%' . $f['value'] . '%');
                }
            }
            $list->setCondition($condition);
        }

        $definitions = [];
        foreach ($list->getOutputDefinitions() as $u) {
            $definitions[] = get_object_vars($u);
        }

        return $this->jsonResponse(['data' => $definitions, 'success' => true, 'total' => $list->getTotalCount()]);

    }

    #[Route('/favorite-output-definitions')]
    public function favoriteOutputDefinitionsAction(Request $request)
    {
        $list = new FavoriteOutputDefinition\Listing();
        $list->setOrder('asc');
        $list->setOrderKey('description');
        $condition = 'classId = ' . $list->quote($request->query->getString('classId'));
        $list->setCondition($condition);

        $definitions = [];
        foreach ($list->getOutputDefinitions() as $u) {
            $definitions[] = get_object_vars($u);
        }

        return $this->jsonResponse(['data' => $definitions, 'success' => true, 'total' => $list->getTotalCount()]);
    }

    #[Route('/save-or-update-favorite-output-definition')]
    public function saveOrUpdateFavoriteOutputDefinitionAction(Request $request)
    {

        $configuration = $request->request->getString('configuration');
        $id = $request->request->getInt('existing');
        $newName = strip_tags($request->request->getString('text'));
        $savedConfig = FavoriteOutputDefinition::getById($id);

        if ($id && $savedConfig) {
            $savedConfig->setConfiguration($configuration);
            $savedConfig->save();

            return $this->jsonResponse(['success' => true]);
        }

        if ($newName) {
            $db = Db::get();
            $list = new FavoriteOutputDefinition\Listing();
            $classId = $request->request->getString('classId');
            $list->setCondition('classId = ' . $list->quote($classId) . ' AND ' . $db->quoteIdentifier('description') . ' = ' . $list->quote($newName));
            $existingOnes = $list->load();
            if (!empty($existingOnes) && !$request->request->getBoolean('force')) {
                return $this->jsonResponse(['success' => false, 'nameexists' => true, 'id' => $existingOnes[0]->getId()]);
            }

            $newConfiguration = new FavoriteOutputDefinition();
            $newConfiguration->setClassId($request->request->getString('classId'));
            $newConfiguration->setDescription($newName);
            $newConfiguration->setConfiguration($configuration);
            $newConfiguration->save();

            return $this->jsonResponse(['success' => true]);
        }

        return $this->jsonResponse(['success' => false]);
    }
}
