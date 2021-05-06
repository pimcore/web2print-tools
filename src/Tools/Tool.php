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
