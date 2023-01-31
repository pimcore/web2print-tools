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

namespace Web2PrintToolsBundle;

use Pimcore\Logger;

/**
 * @method \Web2PrintToolsBundle\FavoriteOutputDefinition\Dao getDao()
 */
class FavoriteOutputDefinition extends \Pimcore\Model\AbstractModel
{
    public $id;
    public $classId;
    public $description;
    public $configuration;

    /**
     * @param $id
     *
     * @return FavoriteOutputDefinition|null
     */
    public static function getById($id)
    {
        try {
            $config = new self();
            $config->getDao()->getById($id);

            return $config;
        } catch (\Exception $ex) {
            Logger::debug($ex->getMessage());

            return null;
        }
    }

    /**
     * @param array $values
     *
     * @return FavoriteOutputDefinition
     */
    public static function create($values = [])
    {
        $config = new self();
        $config->setValues($values);

        return $config;
    }

    /**
     * @return void
     */
    public function save()
    {
        $this->getDao()->save();
    }

    /**
     * @return void
     */
    public function delete()
    {
        $this->getDao()->delete();
    }

    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function setClassId($classId)
    {
        $this->classId = $classId;
    }

    public function getClassId()
    {
        return $this->classId;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
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
