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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Example</title>

    <link rel="stylesheet" type="text/css" href="/website/static/css/print-style.css" media="all" />
    <link rel="stylesheet" type="text/css" href="/website/static/css/print-edit.css" media="screen" />

    <?php if($this->printermarks) { ?>
        <link rel="stylesheet" type="text/css" href="/website/static/css/print-printermarks.css" media="print" />
    <?php } ?>

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

    <?= $this->headLink() ?>
    <?= $this->headScript() ?>

</head>

<body>

    <div id="wrapper">
        <?= $this->layout()->content ?>
    </div>

    <?= $this->inlineScript() ?>
</body>
</html>



