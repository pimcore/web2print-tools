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

namespace Web2PrintToolsBundle\Model\Document\Editable\Outputchanneltable\MetaEntry;

use Web2PrintToolsBundle\Model\Document\Editable\Outputchanneltable\MetaEntry;

class Defaultentry extends MetaEntry
{
    /**
     * @var string
     */
    public $value;

    /**
     * @var bool
     */
    public $span;

    /**
     * @param array $config
     *
     * @return void
     */
    public function setConfig($config)
    {
        parent::setConfig($config);
        $this->setValue($config['value']);
        $this->setSpan($config['span']);
    }

    /**
     * @param string $value
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param bool $span
     */
    public function setSpan($span)
    {
        $this->span = $span;
    }

    /**
     * @return bool
     */
    public function getSpan()
    {
        return $this->span;
    }

    public function __toString()
    {
        return $this->getName() . ': ' . $this->getValue();
    }
}
