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

namespace Web2PrintToolsBundle\FavoriteOutputDefinition\Listing;

use Web2PrintToolsBundle\FavoriteOutputDefinition;

/**
 * @property FavoriteOutputDefinition\Listing $model
 */
class Dao extends \Pimcore\Model\Listing\Dao\AbstractDao
{
    public function load(): array
    {
        $configs = [];

        $unitIds = $this->db->fetchAllAssociative('SELECT id FROM ' . \Web2PrintToolsBundle\FavoriteOutputDefinition\Dao::TABLE_NAME .
                                                 $this->getCondition() . $this->getOrder() . $this->getOffsetLimit());

        foreach ($unitIds as $row) {
            $configs[] = FavoriteOutputDefinition::getById($row['id']);
        }

        $this->model->setOutputDefinitions($configs);

        return $configs;
    }

    public function getTotalCount(): int
    {
        $amount = $this->db->fetchAssociative('SELECT COUNT(*) as amount FROM `' . \Web2PrintToolsBundle\FavoriteOutputDefinition\Dao::TABLE_NAME . '`' . $this->getCondition());

        return $amount['amount'];
    }
}
