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

namespace Web2PrintToolsBundle\Model\Document\Editable;

use OutputDataConfigToolkitBundle\OutputDefinition;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Editable\EditableInterface;
use Pimcore\Model\Document\Editable\EditmodeDataInterface;
use Pimcore\Model\Element\ElementDescriptor;
use Web2PrintToolsBundle\Model\Document\Editable\Outputchanneltable\MetaEntry;

class Outputchanneltable extends Document\Editable implements \Iterator, EditmodeDataInterface
{
    /**
     * @var array
     */
    public $elements = [];

    /**
     * @var array
     */
    public $elementIds = [];

    /**
     * @var string
     */
    public $selectedClass = '';

    /**
     * @var string
     */
    public $selectedFavouriteOutputChannel = '';

    /**
     * @var string
     */
    public $outputChannel = '';

    /**
     * @see \Pimcore\Model\Document\Editable\EditableInterface::getType
     */
    public function getType(): string
    {
        return 'outputchanneltable';
    }

    public function setElements()
    {
        if (empty($this->elements)) {
            $this->elements = [];
            foreach ($this->elementIds as $elementId) {
                if ($elementId['type'] == 'meta') {
                    $subType = $elementId['subtype'] == 'default' ? 'defaultentry' : $elementId['subtype'];
                    $classname = '\\Web2PrintToolsBundle\\Model\\Document\\Editable\\Outputchanneltable\\MetaEntry\\' . ucfirst($subType);

                    if ($subType && class_exists($classname)) {
                        $this->elements[] = new $classname($elementId['path'], $elementId['config']);
                    }
                } else {
                    $el = \Pimcore\Model\Element\Service::getElementById($elementId['type'], $elementId['id']);
                    if ($el instanceof \Pimcore\Model\Element\ElementInterface) {
                        $this->elements[] = $el;
                    }
                }
            }
        }
    }

    /**
     * @see EditableInterface::getData
     */
    public function getData(): array
    {
        $this->setElements();

        return [
            'selectedClass' => $this->selectedClass,
            'elements' => $this->elements,
            'outputChannel' => $this->outputChannel,
            'selectedFavouriteOutputChannel' => $this->selectedFavouriteOutputChannel
        ];
    }

    public function getDataForResource(): array
    {
        return [
            'selectedClass' => $this->selectedClass,
            'elements' => $this->elementIds,
            'outputChannel' => $this->outputChannel,
            'selectedFavouriteOutputChannel' => $this->selectedFavouriteOutputChannel
        ];
    }

    /**
     * Converts the data so it's suitable for the editmode
     */
    public function getDataEditmode(): array
    {
        $this->setElements();
        $return = [];

        if (is_array($this->elements) && count($this->elements) > 0) {
            foreach ($this->elements as $index => $element) {
                if ($element instanceof \Pimcore\Model\DataObject\Concrete) {
                    $return[] = [$element->getId(), $element->getFullPath(), 'object', $element->getClassName()];
                } elseif ($element instanceof \Pimcore\Model\DataObject\AbstractObject) {
                    $return[] = [$element->getId(), $element->getFullPath(), 'object', 'folder'];
                } elseif ($element instanceof MetaEntry) {
                    $subtype = str_replace('Web2PrintToolsBundle\\Model\\Document\\Editable\\Outputchanneltable\\MetaEntry\\', '', get_class($element));

                    //old namespace for compatibility
                    $subtype = str_replace('Web2PrintToolsBundle\\Model\\Document\\Tag\\Outputchanneltable\\MetaEntry\\', '', $subtype);
                    $subtype = str_replace('Pimcore\\Model\\Document\\Tag\\Outputchanneltable\\MetaEntry\\', '', $subtype);

                    $return[] = ['a' . $index, $element->getName(), 'meta', strtolower($subtype), $element->getConfig()];
                }
            }
        }

        return [
            'selectedClass' => $this->selectedClass,
            'selectedFavouriteOutputChannel' => $this->selectedFavouriteOutputChannel,
            'elements' => $return,
            'outputChannel' => $this->outputChannel,
            'documentId' => $this->getDocumentId()
        ];
    }

    public function frontend(): string
    {
        $this->setElements();
        $return = '';

        if (is_array($this->elements) && count($this->elements) > 0) {
            foreach ($this->elements as $element) {
                if ($element instanceof MetaEntry) {
                    $return .= $element->__toString() . '<br />';
                } else {
                    if ($element instanceof ElementDescriptor) {
                        $element = \Pimcore\Model\Element\Service::getElementById($element->getType(), $element->getId());
                    }

                    $return .= \Pimcore\Model\Element\Service::getElementType($element) . ': ' . $element->getFullPath() . '<br />';
                }
            }
        }

        return $return;
    }

    /**
     * @see EditableInterface::setDataFromResource
     *
     * @param mixed $data
     *
     * @return $this
     */
    public function setDataFromResource($data): static
    {
        if ($data = \Pimcore\Tool\Serialize::unserialize($data)) {
            $this->setDataFromEditmode($data);
        }

        return $this;
    }

    /**
     * @see EditableInterface::setDataFromEditmode
     *
     * @param mixed $data
     *
     * @return $this
     */
    public function setDataFromEditmode($data): static
    {
        if (is_array($data['elements'])) {
            $this->elementIds = $data['elements'];
        }
        $this->outputChannel = $data['outputChannel'];
        $this->selectedClass = $data['selectedClass'];
        $this->selectedFavouriteOutputChannel = $data['selectedFavouriteOutputChannel'];

        return $this;
    }

    /**
     * @return \Pimcore\Model\Element\ElementInterface[]
     */
    public function getElements()
    {
        $this->setElements();

        return $this->elements;
    }

    public function isEmpty(): bool
    {
        $this->setElements();

        return count($this->elements) === 0;
    }

    public function resolveDependencies(): array
    {
        $this->setElements();
        $dependencies = [];

        if (is_array($this->elements) && count($this->elements) > 0) {
            foreach ($this->elements as $element) {
                if ($element instanceof \Pimcore\Model\DataObject\AbstractObject) {
                    $key = 'object_' . $element->getId();

                    $dependencies[$key] = [
                        'id' => $element->getId(),
                        'type' => 'object'
                    ];
                }
            }
        }

        return $dependencies;
    }

    public function getFromWebserviceImport($wsElement, $document = null, $params = [], $idMapper = null)
    {
        // currently unsupported
        return [];
    }

    public function __sleep(): array
    {
        $finalVars = [];
        $parentVars = parent::__sleep();
        $blockedVars = ['elements'];
        foreach ($parentVars as $key) {
            if (!in_array($key, $blockedVars)) {
                $finalVars[] = $key;
            }
        }

        return $finalVars;
    }

    public function load()
    {
        $this->setElements();
    }

    public function rewind(): void
    {
        $this->setElements();
        reset($this->elements);
    }

    public function current(): mixed
    {
        $this->setElements();

        return current($this->elements);
    }

    public function key(): mixed
    {
        $this->setElements();

        return key($this->elements);
    }

    public function next(): void
    {
        $this->setElements();

        next($this->elements);
    }

    public function valid(): bool
    {
        $this->setElements();

        return $this->current() !== false;
    }

    /**
     * @return OutputDefinition
     */
    public function getOutputChannel()
    {
        $tmpClass = json_decode($this->outputChannel);
        $config = new OutputDefinition();
        $config->setId($tmpClass->id);
        $config->setChannel($tmpClass->channel);
        $config->setClassId($tmpClass->classId);

        $config->setConfiguration(json_encode($tmpClass->configuration));

        return $config;
    }

    /**
     * @return string
     */
    public function getSelectedClass()
    {
        return $this->selectedClass;
    }
}
