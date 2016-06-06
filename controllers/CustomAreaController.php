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
 * @category   Pimcore
 * @package    EcommerceFramework
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


class Web2Print_CustomAreaController extends \Pimcore\Controller\Action\Admin {

    public function listAction() {
        $areas = array();

        $list = new \Web2Print\CustomArea\Listing();
        $entries = $list->load();
        foreach($entries as $entry) {
            $areas[] = [
                "id" => $entry->getId(),
                "text" => $entry->getName(),
                "expandable" => false,
                "leaf" => true,
                "iconCls" => "plugin_web2print_custom_areas"
            ];
        }
        $this->_helper->json($areas);
    }


    public function createAction() {
        $name = $this->getParam("name");

        $newCustomArea = new \Web2Print\CustomArea();
        $newCustomArea->setName($name);
        $newCustomArea->save();

        $this->_helper->json(array("success" => true, "id" => $newCustomArea->getId()));
    }

    public function deleteAction() {

        $id = $this->getParam("id");
        $customArea = \Web2Print\CustomArea::getById($id);
        if($customArea) {
            $customArea->delete();
        }

        $this->_helper->json(array("success" => true));
    }


    public function getAction() {
        $id = $this->getParam("id");
        $customArea = \Web2Print\CustomArea::getById($id);
        if($customArea) {

            $outputChannel = \Elements\OutputDataConfigToolkit\OutputDefinition::getByO_IdClassIdChannel(0, $customArea->getClassId(), \Web2Print\CustomArea::CUSTOM_AREA_PREFIX . $customArea->getId());
            if($outputChannel) {
                $outputChannel->setDao(null);
            }

            $this->_helper->json(
                array(
                    "id" => $customArea->getId(),
                    "name" => $customArea->getName(),
                    "classId" => $customArea->getClassId(),
                    "description" => $customArea->getDescription(),
                    "type" => $customArea->getType(),
                    "selectedFavouriteOutputChannel" => $customArea->getSelectedFavouriteOutputChannel(),
                    "outputChannel" => $outputChannel,
                    "editmode_css" => $this->loadFile($customArea, "editmode.css"),
                    "editmode_php" => $this->loadFile($customArea, "editmode.php"),
                    "frontend_css" => $this->loadFile($customArea, "frontend.css"),
                    "frontend_php" => $this->loadFile($customArea, "frontend.php"),
                    "active" => $customArea->getActive()
                )
            );


        } else {
            throw new Exception("Custom Area not found.");
        }
    }

    private function loadFile($customArea, $file) {
        $content = "";
        $folder = PIMCORE_WEBSITE_PATH . "/var/areas/web2print_customarea_" . $customArea->getId();

        if(file_exists($folder . "/" . $file)) {
            $content = file_get_contents($folder . "/" . $file);
        }
        return $content;
    }


    private function saveFile($customArea, $file, $content) {
        $folder = PIMCORE_WEBSITE_PATH . "/var/areas/web2print_customarea_" . $customArea->getId();

        if(file_exists($folder . "/" . $file)) {
             file_put_contents($folder . "/" . $file, $content);
        }
    }


    public function saveAction() {
        $id = $this->getParam("id");
        $customArea = \Web2Print\CustomArea::getById($id);
        if($customArea) {
            $data = json_decode($this->getParam("data"));

            //save custom area
            $customArea->setName($data->settings->name);
            $customArea->setDescription($data->settings->description);
            $customArea->setActive($data->settings->active);
            $customArea->setClassId($data->settings->classId);
            $customArea->setType($data->settings->type);
            $customArea->setSelectedFavouriteOutputChannel($this->getParam("selectedFavouriteOutputChannel"));

            $customArea->save();

            // save output channel
            if($this->getParam("outputChannel")) {
                $outputChannelConfig = json_decode($this->getParam("outputChannel"));

                if($outputChannelConfig != null) {
                    if($outputChannelConfig->id) {
                        $outputChannel = \Elements\OutputDataConfigToolkit\OutputDefinition::getById($outputChannelConfig->id);
                    } else {
                        $outputChannel = \Elements\OutputDataConfigToolkit\OutputDefinition::getByO_IdClassIdChannel(0, $outputChannelConfig->o_classId, $outputChannelConfig->channel);
                    }

                    if(empty($outputChannel)) {
                        $outputChannel =  new \Elements\OutputDataConfigToolkit\OutputDefinition();
                    }

                    $outputChannel->setChannel($outputChannelConfig->channel);
                    $outputChannel->setConfiguration(json_encode($outputChannelConfig->configuration));
                    $outputChannel->setO_ClassId($outputChannelConfig->o_classId);

                    $outputChannel->save();
                }
            }

            //save files
            $this->saveFile($customArea, "frontend.php", $data->frontend_php);
            $this->saveFile($customArea, "frontend.css", $data->frontend_css);
            $this->saveFile($customArea, "editmode.php", $data->editmode_php);
            $this->saveFile($customArea, "editmode.css", $data->editmode_css);

            $this->_helper->json(array("success" => true));
        } else {
            throw new Exception("Custom Area not found.");
        }



    }

}