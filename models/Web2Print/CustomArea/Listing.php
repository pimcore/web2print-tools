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


namespace Web2Print\CustomArea;


class Listing extends \Pimcore\Model\Listing\AbstractListing {

    /**
     * @var array
     */
    public $customAreas;

    /**
     * @var array
     */
    public function isValidOrderKey($key) {
        if($key == "id" || $key == "classId" || $key == "description" || $key == "name") {
            return true;
        }
        return false;
    }
 
    /**
     * @return array
     */
    function getCustomAreas() {
        if(empty($this->customAreas)) {
            $this->load();
        }
        return $this->customAreas;
    }

    /**
     * @param array $customAreas
     * @return void
     */
    function setCustomAreas($customAreas) {
        $this->customAreas = $customAreas;
    }

}
