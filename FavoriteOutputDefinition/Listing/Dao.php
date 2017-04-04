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


namespace Web2PrintToolsBundle\FavoriteOutputDefinition\Listing;

use Web2PrintToolsBundle\FavoriteOutputDefinition;

class Dao extends \Pimcore\Model\Listing\Dao\AbstractDao {

    /**
     * @return array
     */
    public function load() {
        $configs = array();

        $unitIds = $this->db->fetchAll("SELECT id FROM " . \Web2PrintToolsBundle\FavoriteOutputDefinition\Dao::TABLE_NAME .
                                                 $this->getCondition() . $this->getOrder() . $this->getOffsetLimit());

        foreach ($unitIds as $row) {
            $configs[] = FavoriteOutputDefinition::getById($row['id']);
        }

        $this->model->setOutputDefinitions($configs);

        return $configs;
    }

    public function getTotalCount() {
        $amount = $this->db->fetchRow("SELECT COUNT(*) as amount FROM `" . \Web2PrintToolsBundle\FavoriteOutputDefinition\Dao::TABLE_NAME . "`" . $this->getCondition());
        return $amount["amount"];
    }

}