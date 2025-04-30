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

namespace Web2PrintToolsBundle\Model\Document\Editable\Outputchanneltable\MetaEntry;

use Web2PrintToolsBundle\Model\Document\Editable\Outputchanneltable\MetaEntry;

class Table extends MetaEntry
{
    /**
     * @var string
     */
    public $values;

    /**
     * @var array
     */
    public $spanCleanedValues;

    /**
     * @var bool
     */
    public $span;

    /**
     * @var int
     */
    private $nextValue = -1;

    /**
     * @param array $config
     *
     * @return void
     */
    public function setConfig($config)
    {
        parent::setConfig($config);
        $this->setValues($config['values']);
    }

    public function setValues($values)
    {
        $this->values = $values;

        $this->spanCleanedValues = [];
        if ($values) {
            foreach ($values as $v) {
                for ($i = 0; $i < $v['span']; $i++) {
                    $this->spanCleanedValues[] = $v['value'];
                }
            }
        }
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getValue($index)
    {
        return $this->spanCleanedValues[$index];
    }

    public function resetNextValue()
    {
        $this->nextValue = -1;
    }

    public function getNextSpanCleanedValue()
    {
        $this->nextValue++;

        return $this->spanCleanedValues[$this->nextValue];
    }

    public function getNextValue()
    {
        $this->nextValue++;

        return $this->values[$this->nextValue];
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
        return $this->getName() . ': ' . $this->getValue(0);
    }
}
