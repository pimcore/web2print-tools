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


$this->headLink()->prependStylesheet(
        array(
            'href' => $this->brick->getPath() . '/area.css',
            'rel' => 'stylesheet',
            'media' => 'screen',
            'type' => 'text/css'
        )
    );
    $this->headLink()->prependStylesheet(
        array(
            'href' => $this->brick->getPath() . '/area-print.css',
            'rel' => 'stylesheet',
            'media' => 'print',
            'type' => 'text/css'
        )
    );
?>

<div class="pagebreak-bottom"></div>
<div class="pagebreak-force-page-break"></div>
<div class="pagebreak-top"></div>
