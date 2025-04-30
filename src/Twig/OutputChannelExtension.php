<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
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
    public function getFunctions(): array
    {
        return [
            new TwigFunction('output_channel_max_group_depth', [$this, 'getMaxGroupDepth']),
            new TwigFunction('output_channel_build_output_data_config', [$this, 'buildOutputDataConfig']),
            new TwigFunction('output_channel_column_header', [$this, 'getColumnHeader']),
        ];
    }

    public function getMaxGroupDepth(array $configArray): int
    {
        return Tool::getMaxGroupDepth($configArray);
    }

    /**
     * @param null $context
     *
     */
    public function buildOutputDataConfig(?OutputDefinition $outputDefinition, $context = null): array
    {
        if ($outputDefinition) {
            return Service::buildOutputDataConfig($outputDefinition, $context);
        }

        return [];
    }

    public function getColumnHeader(IConfigElement $configElement, string $classname): string
    {
        return $configElement->getLabeledValue(new $classname())->label;
    }
}
