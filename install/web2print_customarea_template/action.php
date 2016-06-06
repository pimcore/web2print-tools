<?php

class Document_Tag_Area_web2print_customarea_template extends \Pimcore\Model\Document\Tag\Area\AbstractArea {

    public function action() {

        $this->view->addScriptPath(PIMCORE_DOCUMENT_ROOT . $this->getBrick()->getPath());

        $this->view->dataArray = array();

        $config = $this->loadConfig();
        $object = $this->emptyObjectInstance($config->classId);
        if($object) {
            $this->view->selectedClassName = $object->getClass()->getName();
            $configArray = Elements\OutputDataConfigToolkit\Service::getOutputDataConfig($this->emptyObjectInstance($config->classId), $config->id);

            foreach($this->view->customareatable("table") as $element) {
                $entry = array();
                foreach($configArray as $configElement) {
                    $outputElement = $configElement->getLabeledValue($element);
                    $entry[$outputElement->label] = $outputElement->value;
                }
                $this->view->dataArray[] = $entry;
            }
        }
    }

    private function loadConfig() {
        return new Zend_Config_Xml(PIMCORE_DOCUMENT_ROOT . $this->getBrick()->getPath() . "/area.xml");
    }

    private function emptyObjectInstance($classId) {
        $class = \Pimcore\Model\Object\ClassDefinition::getById($classId);
        if($class) {
            $classname = "Object_" . $class->getName();
            $object = new $classname();
            $object->setO_id(0);
            return $object;
        }
        return null;
    }
}