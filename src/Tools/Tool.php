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

namespace Web2PrintToolsBundle\Tools;

use OutputDataConfigToolkitBundle\ConfigElement\Operator\Group;

class Tool
{
    public static function getMaxGroupDepth($configArray, $level = 1)
    {
        $groupFound = false;
        foreach ($configArray as $configElement) {
            if ($configElement instanceof Group) {
                if (!$groupFound) {
                    $level++;
                    $groupFound = true;
                }

                $subLevel = self::getMaxGroupDepth($configElement->getChilds(), $level);

                if ($subLevel > $level) {
                    $level = $subLevel;
                }
            }
        }

        return $level;
    }
}
