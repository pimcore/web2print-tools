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

namespace Web2PrintToolsBundle\Twig;

use OutputDataConfigToolkitBundle\ConfigElement\IConfigElement;
use OutputDataConfigToolkitBundle\OutputDefinition;
use OutputDataConfigToolkitBundle\Service;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Web2PrintToolsBundle\Tools\Tool;

class OutputChannelExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('output_channel_max_group_depth', [$this, 'getMaxGroupDepth']),
            new TwigFunction('output_channel_build_output_data_config', [$this, 'buildOutputDataConfig']),
            new TwigFunction('output_channel_column_header', [$this, 'getColumnHeader']),
        ];
    }

    /**
     * @param array $configArray
     *
     * @return int
     */
    public function getMaxGroupDepth(array $configArray): int
    {
        return Tool::getMaxGroupDepth($configArray);
    }

    /**
     * @param OutputDefinition|null $outputDefinition
     * @param null $context
     *
     * @return array
     */
    public function buildOutputDataConfig(?OutputDefinition $outputDefinition, $context = null): array
    {
        if ($outputDefinition) {
            return Service::buildOutputDataConfig($outputDefinition, $context);
        }

        return [];
    }

    /**
     * @param IConfigElement $configElement
     * @param string $classname
     *
     * @return string
     */
    public function getColumnHeader(IConfigElement $configElement, string $classname): string
    {
        return $configElement->getLabeledValue(new $classname())->label;
    }
}
