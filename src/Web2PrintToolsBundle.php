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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


namespace Web2PrintToolsBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Web2PrintToolsBundle\Tools\Installer;

class Web2PrintToolsBundle extends AbstractPimcoreBundle
{
    /**
     * @inheritDoc
     */
    public function getCssPaths()
    {
        return [
            '/bundles/web2printtools/css/admin.css'
        ];
    }
    
    public function getJsPaths()
    {
        return [
            '/bundles/web2printtools/js/Web2Print/bundle.js',
            '/bundles/web2printtools/js/Web2Print/favoriteOutputDefinitions.js',
            '/bundles/web2printtools/js/Web2Print/saveAsFavouriteOutputDefinitionDialog.js',
            '/bundles/web2printtools/js/pimcore/document/tags/metaentry/abstract.js',
            '/bundles/web2printtools/js/pimcore/document/tags/metaentry/defaultentry.js',
            '/bundles/web2printtools/js/pimcore/document/tags/metaentry/table.js'
        ];
    }
    
    public function getEditmodeJsPaths()
    {
        return [
            '/bundles/web2printtools/js/pimcore/document/tags/outputchanneltable.js',
        ];
    }
    
    public function getEditmodeCssPaths()
    {
        return [
            '/bundles/web2printtools/css/admin.css'
        ];
    }

    /**
     * @return Installer
     */
    public function getInstaller()
    {
        return new Installer();
    }

}
