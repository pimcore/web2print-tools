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


pimcore.registerNS("pimcore.plugin.Web2Print.customAreas.panel");

pimcore.plugin.Web2Print.customAreas.panel = Class.create({

    /**
     * @var string
     */
    layoutId: "",

    /**
     * constructor
     * @param layoutId
     */
    initialize: function(layoutId) {
        this.layoutId = layoutId;
        this.getLayout();
    },


    /**
     * activate panel
     */
    activate: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.setActiveItem( this.layoutId );
    },


    /**
     * create tab panel
     * @returns Ext.Panel
     */
    getLayout: function () {

        if (!this.layout) {

            // create new panel
            this.layout = new Ext.Panel({
                id: this.layoutId,
                title: t("plugin_web2print_custom_areas"),
                iconCls: "plugin_web2print_custom_areas plugin_web2print_custom_areas_overlay",
                border: false,
                layout: "border",
                closable: true,

                // layout...
                items: [
                    this.getTree(),         // item tree, left side
                    this.getTabPanel()    // edit page, right side
                ]
            });

            // add event listener
            var layoutId = this.layoutId;
            this.layout.on("destroy", function () {
                pimcore.globalmanager.remove( layoutId );
            }.bind(this));

            // add panel to pimcore panel tabs
            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add( this.layout );
            tabPanel.setActiveItem( this.layoutId );

            // update layout
            pimcore.layout.refresh();
        }

        return this.layout;
    },


    getTree: function () {
        if (!this.tree) {

            this.store = Ext.create('Ext.data.TreeStore', {
                autoLoad: false,
                autoSync: true,
                proxy: {
                    type: 'ajax',
                    url: '/plugin/Web2Print/Custom-Area/list',
                    reader: {
                        type: 'json',
                        totalProperty : 'total',
                        rootProperty: 'nodes'

                    }
                }
            });

            this.tree = new Ext.tree.TreePanel({
                region: "west",
                store: this.store,
                useArrows:true,
                autoScroll:true,
                animate:true,
                containerScroll: true,
                width: 200,
                split: true,
                root: {
                    id: '0'
                },
                listeners: {
                    itemclick: function(tree, record, item, index, e, eOpts) {
                        this.openCustomArea(record.data.id);
                    }.bind(this),
                    itemcontextmenu: function ( tree, record, item, index, e, eOpts ) {
                        e.stopEvent();
                        tree.select();

                        var menu = new Ext.menu.Menu();
                        menu.add(new Ext.menu.Item({
                            text: t('delete'),
                            iconCls: "pimcore_icon_delete",
                            handler: this.deleteCustomArea.bind(this, tree, record)
                        }));

                        menu.showAt(e.pageX, e.pageY);
                    }.bind(this)
                },
                rootVisible: false,
                tbar: {
                    items: [
                        {
                            // add button
                            text: t("plugin_web2print_custom_area_add"),
                            iconCls: "pimcore_icon_add",
                            handler: this.addCustomArea.bind(this)
                        }
                    ]
                }
            });

            this.tree.on("render", function () {
                this.getRootNode().expand();
            });
        }

        return this.tree;
    },


    addCustomArea: function () {
        Ext.MessageBox.prompt(t('plugin_web2print_custom_area_add'), t('plugin_web2print_custom_area_add_text'),
                                                this.addCustomAreaComplete.bind(this), null, null, "");
    },


    /**
     * save added item
     * @param button
     * @param value
     * @param object
     * @todo ...
     */
    addCustomAreaComplete: function (button, value, object) {

        var regresult = value.match(/[a-zA-Z0-9]+/);
        if (button == "ok" && value.length > 2 && regresult == value) {
            Ext.Ajax.request({
                url: "/plugin/Web2Print/Custom-Area/create",
                params: {
                    name: value
                },
                success: function (response) {
                    this.tree.getStore().load();

                    var data = Ext.decode(response.responseText);

                    if(!data || !data.success) {
                        Ext.Msg.alert(t('plugin_web2print_custom_area_add'), t('plugin_web2print_custom_area_add_problem'));
                    } else {
                        this.openCustomArea(intval(data.id));
                    }
                }.bind(this)
            });
        } else if (button == "cancel") {
            return;
        }
        else {
            Ext.Msg.alert(t('plugin_web2print_custom_area_add'), t('plugin_web2print_custom_area_add_problem'));
        }
    },


    /**
     * delete existing rule
     */
    deleteCustomArea: function (tree, record) {
        Ext.Msg.confirm(t('delete'), t('delete_message'), function(btn) {
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url: "/plugin/Web2Print/Custom-Area/delete",
                    params: {
                        id: record.data.id
                    },
                    success: function () {
                        record.remove();
                    }.bind(this)
                });
            }
        }.bind(this));
    },


    openCustomArea: function (id) {

        // load defined rules
        Ext.Ajax.request({
            url: "/plugin/Web2Print/Custom-Area/get",
            params: {
                id: id
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);
                var item = new pimcore.plugin.Web2Print.customAreas.item(this, res);
            }.bind(this)
        });

    },


    getTabPanel: function () {
        if (!this.panel) {
            this.panel = new Ext.TabPanel({
                region: "center",
                border: false
            });
        }

        return this.panel;
    }
});
