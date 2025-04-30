/**
* This source file is available under the terms of the
* Pimcore Open Core License (POCL)
* Full copyright and license information is available in
* LICENSE.md which is distributed with this source code.
*
*  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.com)
*  @license    Pimcore Open Core License (POCL)
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