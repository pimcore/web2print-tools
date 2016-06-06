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


$levels = \Web2Print\Tool::getMaxGroupDepth($this->configArray);
?>

<thead>
    <?= $this->partial("/specAttribute/column-attribute-table-header.php",
        array("configArray" => $this->configArray, "classname" => $this->classname, "levels" => $levels, "currentLevel" => 0));
    ?>
</thead>

<tbody>
    <?= $this->partial("/specAttribute/column-attribute-table-values.php",
        array("configArray" => $this->configArray, "elements" => $this->elements, "thumbnailName" => $this->thumbnailName));
    ?>
</tbody>
