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


if(!is_object($this->outputElement->value)) { ?>

    <?php
        if($this->outputElement->def instanceof \Pimcore\Model\Object\ClassDefinition\Data\Select) {
            $value = $this->translateAdmin($this->outputElement->value);
        } else {
            $value = $this->outputElement->value;
        }
    ?>

    <?= $value ?>

<?php } else if($this->outputElement->value instanceof Object_Data_DimensionUnitField) { ?>
    <?php
        $value = $this->outputElement->value->getValue();
        $dimension = $this->outputElement->value->getUnit()->getAbbreviation();
    ?>

    <span class="unit"><?= $dimension ?></span>
    <?= $value ?>
<?php } else if($this->outputElement->value instanceof \Pimcore\Model\Asset\Image) { ?>

    <img src="<?= $this->outputElement->value->getThumbnail($this->thumbnailName)?>" tag="image" />

<?php } else if($this->outputElement->value instanceof \Pimcore\Model\Object\Data\StructuredTable) { ?>
    <?php
        $cols = array();
        $colKeys = array();
        foreach($this->outputElement->def->getCols() as $c) {
            $cols[] = $this->view->translateAdmin($c["label"]);
            $colKeys[] = $c["key"];
        }

        $rows = array();
        $rowKeys = array();
        foreach($this->outputElement->def->getRows() as $r) {
            $rows[] = $this->view->translateAdmin($r["label"]);
            $rowKeys[] = $r["key"];
        }

        $value = $this->outputElement->value;
    ?>

    <table class="table">
        <tr class="subTableHeader">
            <th></th>
            <?php foreach($cols as $c) { ?>
            <td class="txtCenter width80"><?= $c ?></td>
            <?php } ?>
        </tr>

        <?php foreach($rows as $i => $r) { ?>

        <tr>
            <td class="subTableHeader">
                <?= $r ?>
            </td>

            <?php foreach($colKeys as $c) { ?>
                <?php
                    $rKey = $rowKeys[$i];
                    $getter = "get" . $rKey . "__" . $c;
                ?>

                <td class="txtCenter"><?= $value->$getter()?></td>
            <?php } ?>

        </tr>
        <?php } ?>

    </table>
<?php } ?>