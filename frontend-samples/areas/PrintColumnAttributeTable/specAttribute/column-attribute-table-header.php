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
?>

    <tr>
        <?php foreach($this->configArray as $configElement) { ?>

            <?php if($configElement instanceof Elements\OutputDataConfigToolkit\ConfigElement\Operator\Group) { ?>

                <td colspan="<?=count($configElement->getChilds()) ?>">
                    <?php $classname = $this->classname;?>
                    <?= $configElement->getLabel() ?>
                </td>

                <?= $this->partial("/specAttribute/column-attribute-table-header.php",
                    array("configArray" => $configElement->getChilds(), "classname" => $this->classname, "levels" => $this->levels, "currentLevel" => $this->currentLevel + 1));
                ?>


            <?php } else { ?>
                <td rowspan="<?= $this->levels - $this->currentLevel ?>">
                    <?php $classname = $this->classname;?>
                    <?= $configElement->getLabeledValue(new $classname())->label ?>
                </td>
            <?php } ?>
        <?php } ?>
    </tr>