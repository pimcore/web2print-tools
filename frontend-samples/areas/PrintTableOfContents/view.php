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


/**
 * @var $this Pimcore_View
 * @var $brick Document_Tag_Area_Info
 */
$brick = $this->brick;

if (!(is_array($this->placeholder("__areas")->getValue()) and in_array($brick->getId(),$this->placeholder("__areas")->getValue()))) {
    $this->placeholder("__areas")->append($this->brick->getId());

    $this->headLink()->prependStylesheet(
        array(
            'href' => $brick->getPath() . '/area.css',
            'rel' => 'stylesheet',
            'media' => 'all',
            'type' => 'text/css'
        ));
}
?>

<div id="toc">TABLE OF CONTENTS IS GENERATED ON PDF-EXPORT</div>

<?php $this->inlineScript()->appendFile($brick->getPath() . "/toc.js");