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


namespace Web2PrintToolsBundle\Document\Tag;

use OutputDataConfigToolkitBundle\OutputDefinition;
use \Pimcore\Model\Document;
use Web2PrintToolsBundle\Document\Tag\Outputchanneltable\MetaEntry;

class Outputchanneltable extends Document\Tag implements \Iterator {

    /**
     * @var array
     */
    public $elements = array();

    /**
     * @var array
     */
    public $elementIds = array();

    /**
     * @var string
     */
    public $selectedClass = "";

    /**
     * @var string
     */
    public $selectedFavouriteOutputChannel = "";

    /**
     * @var string
     */
    public $outputChannel = "";

     /**
     * @see \Pimcore\Model\Document\Tag\TagInterface::getType
     * @return string
     */
    public function getType() {
        return "outputchanneltable";
    }

    /*
     *
     */
    public function setElements() {
        if(empty($this->elements)) {
            $this->elements = array();
            foreach ($this->elementIds as $elementId) {
                if($elementId["type"] == "meta") {
                    $subType = $elementId["subtype"] == 'default' ? 'defaultentry' : $elementId["subtype"];
                    $classname = "\\Web2PrintToolsBundle\\Model\\Document\\Tag\\Outputchanneltable\\MetaEntry\\" . ucfirst($subType);

                    if($subType && class_exists($classname)) {
                        $this->elements[] = new $classname($elementId["path"], $elementId['config']);
                    }
                } else {
                    $el = \Pimcore\Model\Element\Service::getElementById($elementId["type"], $elementId["id"]);
                    if($el instanceof \Pimcore\Model\Element\ElementInterface) {
                        $this->elements[] = $el;
                    }
                }
            }
        }
    }

    /**
     * @see \Pimcore\Model\Document\Tag\TagInterface::getData
     * @return \stdClass|void
     */
    public function getData() {
        $this->setElements();

        $data = array(
            'selectedClass' => $this->selectedClass,
            'elements' =>  $this->elements,
            'outputChannel' => $this->outputChannel,
            'selectedFavouriteOutputChannel' => $this->selectedFavouriteOutputChannel
        );

        return $data;
    }

    /**
     * @return \stdClass|void
     */
    public function getDataForResource() {

        $data = array(
            'selectedClass' => $this->selectedClass,
            'elements' =>  $this->elementIds,
            'outputChannel' => $this->outputChannel,
            'selectedFavouriteOutputChannel' => $this->selectedFavouriteOutputChannel
        );

        return $data;
    }

    /**
     * Converts the data so it's suitable for the editmode
     * @return mixed
     */
    public function getDataEditmode() {

        $this->setElements();
        $return = array();

        if (is_array($this->elements) && count($this->elements) > 0) {
            foreach ($this->elements as $index => $element) {
                if ($element instanceof \Pimcore\Model\DataObject\Concrete) {
                    $return[] = array($element->getId(), $element->getFullPath(), "object", $element->getClassName());
                }
                else if ($element instanceof \Pimcore\Model\DataObject\AbstractObject) {
                    $return[] = array($element->getId(), $element->getFullPath(), "object", "folder");
                }
                else if($element instanceof MetaEntry) {

                    $subtype = str_replace("Web2PrintToolsBundle\\Model\\Document\\Tag\\Outputchanneltable\\MetaEntry\\", "", get_class($element));

                    //old namespace for compatibility
                    $subtype = str_replace("Pimcore\\Model\\Document\\Tag\\Outputchanneltable\\MetaEntry\\", "", get_class($element));

                    $return[] = array("a" . $index, $element->getName(), "meta", strtolower($subtype), $element->getConfig());
                }
            }
        }

        $data = array(
            'selectedClass' => $this->selectedClass,
            'selectedFavouriteOutputChannel' => $this->selectedFavouriteOutputChannel,
            'elements' =>  $return,
            'outputChannel' => $this->outputChannel,
            'documentId' => $this->getDocumentId()
        );

        return $data;
    }

    /**
     * @see \Pimcore\Model\Document\Tag\TagInterface::frontend
     * @return void
     */
    public function frontend() {

        $this->setElements();
        $return = "";

        if (is_array($this->elements) && count($this->elements) > 0) {
            foreach ($this->elements as $element) {
                if($element instanceof MetaEntry) {
                    $return .= $element->__toString() . "<br />";
                } else {
                    $return .= \Pimcore\Model\Element\Service::getElementType($element) . ": " . $element->getFullPath() . "<br />";
                }
            }
        }

        return $return;
    }

    /**
     * @see Document_Tag_Interface::setDataFromResource
     * @param mixed $data
     * @return void
     */
    public function setDataFromResource($data) {
        if($data = \Pimcore\Tool\Serialize::unserialize($data)) {
            $this->setDataFromEditmode($data);
        }
    }

    /**
     * @see \Pimcore\Model\Document\Tag\TagInterface::setDataFromEditmode
     * @param mixed $data
     * @return void
     */
    public function setDataFromEditmode($data) {

        if(is_array($data['elements'])) {
            $this->elementIds = $data['elements'];
        }
        $this->outputChannel = $data['outputChannel'];
        $this->selectedClass = $data['selectedClass'];
        $this->selectedFavouriteOutputChannel = $data['selectedFavouriteOutputChannel'];
    }

    /**
     * @return \Pimcore\Model\Element\ElementInterface[]
     */
    public function getElements() {
        $this->setElements();
        return $this->elements;
    }

    /**
     * @return boolean
     */
    public function isEmpty() {
        $this->setElements();
        return count($this->elements) > 0 ? false : true;
    }

    /**
     * @return array
     */
    public function resolveDependencies() {

        $this->setElements();
        $dependencies = array();

        if (is_array($this->elements) && count($this->elements) > 0) {
            foreach ($this->elements as $element) {
                if ($element instanceof \Pimcore\Model\DataObject\AbstractObject) {

                    $key = "object_" . $element->getO_Id();

                    $dependencies[$key] = array(
                        "id" => $element->getO_Id(),
                        "type" => "object"
                    );
                }
            }
        }

        return $dependencies;
    }

    public function getFromWebserviceImport($wsElement, $document = null, $params = [], $idMapper = null) {
        // currently unsupported
        return array();
    }


    /**
     * @return array
     */
    public function __sleep() {

        $finalVars = array();
        $parentVars = parent::__sleep();
        $blockedVars = array("elements");
        foreach ($parentVars as $key) {
            if (!in_array($key, $blockedVars)) {
                $finalVars[] = $key;
            }
        }

        return $finalVars;
    }

    /**
     *
     */
    public function load () {
        $this->setElements();
    }

    /**
     * Methods for Iterator
     */

    public function rewind() {
        $this->setElements();
        reset($this->elements);
    }

    public function current() {
        $this->setElements();
        $var = current($this->elements);
        return $var;
    }

    public function key() {
        $this->setElements();
        $var = key($this->elements);
        return $var;
    }

    public function next() {
        $this->setElements();
        $var = next($this->elements);
        return $var;
    }

    public function valid() {
        $this->setElements();
        $var = $this->current() !== false;
        return $var;
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
        $config->setO_ClassId($tmpClass->o_classId);
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
