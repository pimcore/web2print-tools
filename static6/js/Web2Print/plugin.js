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


pimcore.registerNS("pimcore.plugin.web2print");

pimcore.plugin.web2print = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.web2print";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    uninstall: function () {
    },

    pimcoreReady: function (params, broker) {
        var user = pimcore.globalmanager.get("user");
        var toolbar = pimcore.globalmanager.get("layout_toolbar");
        var perspectiveCfg = pimcore.globalmanager.get("perspective");

        if (perspectiveCfg.inToolbar("web2print")) {
            // init
            var menuItems = toolbar.web2printMenu;
            if (!menuItems) {
                menuItems = new Ext.menu.Menu({cls: "pimcore_navigation_flyout"});
                toolbar.web2printMenu = menuItems;
            }

            if (user.isAllowed("plugin_web2print_custom_area")) {

                // create item
                var panelId = "plugin_web2print_customAreas_config";
                var item = {
                    text: t("plugin_web2print_customAreas_config"),
                    iconCls: "plugin_web2print_custom_areas plugin_web2print_custom_areas_overlay",
                    handler: function () {
                        try {
                            pimcore.globalmanager.get(panelId).activate();
                        }
                        catch (e) {
                            pimcore.globalmanager.add(panelId, new pimcore.plugin.Web2Print.customAreas.panel(panelId));
                        }
                    }
                };
                // add to menu
                menuItems.add(item);
            }

            if (perspectiveCfg.inToolbar("web2print.favorite_outputdefinitions") && user.isAllowed("plugin_web2print_favourite_output_channels")) {
                menuItems.add({
                    text: t("web2print_favorite_outputdefinitions"),
                    iconCls: "plugin_outputdataconfig_icon plugin_outputdataconfig_icon_overlay",
                    cls: "pimcore_main_menu",
                    handler: function () {
                        try {
                            pimcore.globalmanager.get("web2print.favorite_outputdefinitions").activate();
                        }
                        catch (e) {
                            pimcore.globalmanager.add("web2print.favorite_outputdefinitions", new pimcore.plugin.web2print.favoriteOutputDefinitionsTable());
                        }
                    }
                });
            }

            if (menuItems.items.length > 0) {

                var insertPoint = Ext.get("pimcore_menu_settings");
                if (!insertPoint) {
                    var dom = Ext.dom.Query.select('#pimcore_navigation ul li:last');
                    insertPoint = Ext.get(dom[0]);
                }

                this.navEl = Ext.get("pimcore_menu_web2print");
                if (!this.navEl) {
                    this.navEl = Ext.get(
                        insertPoint.insertHtml(
                            "afterEnd",
                            '<li id="pimcore_menu_web2print" class="pimcore_menu_item icon-print">' + t('plugin_web2print_mainmenu') + '</li>'
                        )
                    );
                }

                this.navEl.on("mousedown", toolbar.showSubMenu.bind(menuItems));
            }
        }
    }
});

new pimcore.plugin.web2print();