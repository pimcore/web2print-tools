<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\Web2PrintToolsBundle;

use Pimcore\Bundle\Web2PrintToolsBundle\DependencyInjection\Web2PrintToolsExtension;
use Pimcore\Bundle\Web2PrintToolsBundle\Tools\Installer;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Installer\InstallerInterface;
use Pimcore\Extension\Bundle\PimcoreBundleAdminClassicInterface;
use Pimcore\Extension\Bundle\Traits\BundleAdminClassicTrait;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class Web2PrintToolsBundle extends AbstractPimcoreBundle implements PimcoreBundleAdminClassicInterface
{
    use BundleAdminClassicTrait;
    use PackageVersionTrait;

    protected function getComposerPackageName(): string
    {
        return 'pimcore/web2print-tools-bundle';
    }

    public function getContainerExtension(): ExtensionInterface
    {
        return new Web2PrintToolsExtension();
    }

    public function getCssPaths(): array
    {
        return [
            '/bundles/web2printtools/css/admin.css',
        ];
    }

    public function getJsPaths(): array
    {
        return [
            '/bundles/web2printtools/js/Web2Print/bundle.js',
            '/bundles/web2printtools/js/Web2Print/favoriteOutputDefinitions.js',
            '/bundles/web2printtools/js/Web2Print/saveAsFavouriteOutputDefinitionDialog.js',
            '/bundles/web2printtools/js/pimcore/document/editables/metaentry/abstract.js',
            '/bundles/web2printtools/js/pimcore/document/editables/metaentry/defaultentry.js',
            '/bundles/web2printtools/js/pimcore/document/editables/metaentry/table.js',
        ];
    }

    public function getEditmodeJsPaths(): array
    {
        return [
            '/bundles/web2printtools/js/pimcore/document/editables/outputchanneltable.js',
        ];
    }

    public function getEditmodeCssPaths(): array
    {
        return [
            '/bundles/web2printtools/css/admin.css',
        ];
    }

    public function getInstaller(): ?InstallerInterface
    {
        return $this->container->get(Installer::class);
    }
}
