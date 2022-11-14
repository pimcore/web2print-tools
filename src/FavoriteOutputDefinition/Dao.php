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

namespace Web2PrintToolsBundle\FavoriteOutputDefinition;

use Pimcore\Db\Helper;

class Dao extends \Pimcore\Model\Dao\AbstractDao
{
    const TABLE_NAME = 'bundle_web2print_favorite_outputdefinitions';

    /**
     * Contains all valid columns in the database table
     *
     * @var array
     */
    protected $validColumns = [];

    /**
     * Get the valid columns from the database
     *
     * @return void
     */
    public function init()
    {
        $this->validColumns = $this->getValidTableColumns(self::TABLE_NAME);
    }

    /**
     * @return void
     */
    public function getById($id)
    {
        $outputDefinitionRaw = $this->db->fetchAssociative('SELECT * FROM ' . self::TABLE_NAME . ' WHERE id=?', [$id]);
        if (empty($outputDefinitionRaw)) {
            throw new \Exception('OutputDefinition-Id ' . $id . ' not found.');
        }
        $this->assignVariablesToModel($outputDefinitionRaw);
    }

    /**
     * Create a new record for the object in database
     *
     * @return bool
     */
    public function create()
    {
        $this->db->insert(self::TABLE_NAME, []);
        $this->model->setId($this->db->lastInsertId());

        return $this->save();
    }

    /**
     * @return bool|void
     */
    public function save()
    {
        if ($this->model->getId()) {
            $this->update();

            return;
        }

        return $this->create();
    }

    /**
     * @return void
     */
    public function update()
    {
        $class = get_object_vars($this->model);

        $data = [];

        foreach ($class as $key => $value) {
            if (in_array($key, $this->validColumns)) {
                if (is_array($value) || is_object($value)) {
                    $value = serialize($value);
                } elseif (is_bool($value)) {
                    $value = (int)$value;
                }
                $data[$key] = $value;
            }
        }
        Helper::insertOrUpdate($this->db, self::TABLE_NAME, $data);
    }

    /**
     * Deletes object from database
     *
     * @return void
     */
    public function delete()
    {
        $this->db->executeStatement('DELETE FROM ' . $this->db->quoteIdentifier(self::TABLE_NAME) . ' where id = ?', [$this->model->getId()]);
    }
}
