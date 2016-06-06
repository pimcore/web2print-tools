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


if($this->editmode) { ?>
    <h4><?php echo $this->input("headline") ?></h4>
    <p><?php echo $this->textarea("headtext") ?></p>

    <?php echo $this->outputchanneltable("tableconfig") ?>
<?php } else { ?>

    <div class="headline">
        <h4><?php echo $this->input("headline") ?></h4>
        <p><?php echo $this->textarea("headtext") ?></p>
    </div>
    <?php
        $configArray = array();
        if($this->outputchanneltable("tableconfig")->getOutputChannel()) {
            $configArray = Elements\OutputDataConfigToolkit\Service::buildOutputDataConfig($this->outputchanneltable("tableconfig")->getOutputChannel());
        }
        ?>
    <table class="outputchannel">
        <?= $this->partial("/specAttribute/column-attribute-table.php",
            array("configArray" => $configArray,
                  "classname" => "Object_" . $this->outputchanneltable("tableconfig")->getSelectedClass(),
                  "elements" => $this->outputchanneltable("tableconfig"),
                  "thumbnailName" => "print_image_small"
            )
            ); ?>
    </table>

<?php } ?>