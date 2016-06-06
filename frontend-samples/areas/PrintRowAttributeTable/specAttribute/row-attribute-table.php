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


<?php
    $isFirst = !$this->subgroup;
    $count = 0;
    $totalCount = count($this->configArray);
?>
<?php if($isFirst) { ?>
    <thead>
<?php } ?>

<?php foreach($this->configArray as $configElement) { ?>

    <?php
        $even = $this->even;
        if(!$this->subgroup && ($count % 2 == 0)) {
            $even = "even";
        }
    ?>


    <?php if($configElement instanceof Elements\OutputDataConfigToolkit\ConfigElement\Operator\Group) { ?>
        <tr class="row-attribute-table-group <?= $even ?>">
            <td colspan="<?= count($this->elements->getElements()) + 1 ?>"><?= $configElement->getLabel(); ?></td>
        </tr>
        <?= $this->partial("/specAttribute/row-attribute-table.php",
            array("configArray" => $configElement->getChilds(), "classname" => $classname, "elements" => $this->elements, "subgroup" => true, "even" => $even));
        ?>
    <?php } else { ?>

        <tr class="<?= $even ?>">
            <td>
                <?php $classname = $this->classname;?>
                <?= $configElement->getLabeledValue(new $classname())->label ?>
            </td>

            <?php foreach($this->elements as $element) { ?>

                <?php if($element instanceof \Pimcore\Model\Document\Tag\Outputchanneltable\MetaEntry\Defaultentry) { ?>
                    <?php if($element->getSpan()) { ?>
                        <?php if($isFirst) { ?>
                            <td><?= $element->getName() ?></td>
                        <?php } else if($count < 2) { ?>
                            <td rowspan="<?= $totalCount-1 ?>"><?= $element->getValue() ?></td>
                        <?php } ?>
                    <?php } else { ?>
                        <td><?= $element->getValue() ?></td>
                    <?php } ?>
                <?php } else if($element instanceof \Pimcore\Model\Document\Tag\Outputchanneltable\MetaEntry\Table) { ?>
                    <?php
                        if($isFirst && !$this->subgroup) {
                            $element->resetNextValue();
                        }
                    ?>
                    <td><?= $element->getNextSpanCleanedValue() ?></td>
                <?php } else { ?>
                    <?php $outputElement = $configElement->getLabeledValue($element); ?>
                    <td>
                        <?= $this->partial("/specAttribute/spec-value.php",
                            array(
                                "outputElement" => $outputElement,
                                "thumbnailName" => $this->thumbnailName
                            )) ?>
                    </td>

                <?php } ?>
            <?php } ?>

        </tr>
    <?php } ?>

    <?php if($isFirst) { ?>
        </thead>
        <tbody>
        <?php $isFirst = false; ?>
    <?php } ?>

    <?php $count++; ?>

<?php } ?>

<?php if(!$this->subgroup) { ?>
    </tbody>
<?php } ?>
