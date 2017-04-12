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


namespace Web2PrintToolsBundle\Document\Tag\Outputchanneltable\MetaEntry;


use Web2PrintToolsBundle\Document\Tag\Outputchanneltable\MetaEntry;

class Defaultentry extends MetaEntry {

    /**
     * @var string
     */
    public $value;

    /**
     * @var bool
     */
    public $span;

    public function setConfig($config) {
        parent::setConfig($config);
        $this->setValue($config['value']);
        $this->setSpan($config['span']);
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param boolean $span
     */
    public function setSpan($span)
    {
        $this->span = $span;
    }

    /**
     * @return boolean
     */
    public function getSpan()
    {
        return $this->span;
    }


    public function __toString()
    {
        return $this->getName() . ": " . $this->getValue();
    }

}