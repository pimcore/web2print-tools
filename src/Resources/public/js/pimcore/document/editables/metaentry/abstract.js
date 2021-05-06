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


pimcore.registerNS("pimcore.document.editables.metaentry.abstract");
pimcore.document.editables.metaentry.abstract = Class.create({

    type: "meta",
    subtype: "abstract",

    getInitData: function(id) {

        var initData = {
            id: id,
            type: this.type,
            subtype: this.subtype
        };

        return initData;
    }

});