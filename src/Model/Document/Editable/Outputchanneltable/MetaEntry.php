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

namespace Web2PrintToolsBundle\Model\Document\Editable\Outputchanneltable;

class MetaEntry
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $config;

    /**
     * @param string|null $name
     * @param array|null $config
     */
    public function __construct($name = null, $config = [])
    {
        $this->setName($name);
        $this->setConfig($config);
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
