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


pimcore.registerNS("pimcore.bundle.web2print.favoriteOutputDefinitionsTable");
pimcore.bundle.web2print.favoriteOutputDefinitionsTable = Class.create({

    dataUrl: '/admin/web2printtools/admin/favorite-output-definitions-table-proxy',

    initialize: function () {
        this.getTabPanel();
    },

    activate: function (filter) {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.setActiveItem("bundle_web2print_favorite_outputdefinitions");
    },

    getHint: function () {
        return "";
    },

    getTabPanel: function () {
        if (!this.panel) {
            this.panel = new Ext.Panel({
                id: "bundle_web2print_favorite_outputdefinitions",
                iconCls: "bundle_outputdataconfig_icon",
                title: t("web2print_favorite_outputdefinitions"),
                border: false,
                layout: "fit",
                closable: true,
                items: [this.createGrid()]
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem("bundle_web2print_favorite_outputdefinitions");

            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("web2print.favorite_outputdefinitions");
            }.bind(this));

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    createGrid: function (response) {
        var itemsPerPage = pimcore.helpers.grid.getDefaultPageSize();

        this.store = pimcore.helpers.grid.buildDefaultStore(
            this.dataUrl,
            [
                {name: 'id'},
                {name: 'description'},
                {name: 'classId'},
                {name: 'configuration'}
            ],
            itemsPerPage
        );
        this.pagingtoolbar = pimcore.helpers.grid.buildDefaultPagingToolbar(this.store);

        var gridColumns = [];

        gridColumns.push({header: "ID", width: 40, sortable: true, dataIndex: 'id'});
        gridColumns.push({
            header: t("description"),
            flex: 200,
            sortable: true,
            dataIndex: 'description',
            renderer: Ext.util.Format.htmlEncode,
            filter: 'string',
            editor: new Ext.form.TextField({})
        });
        gridColumns.push({
            header: t("class"), width: 200, sortable: true, dataIndex: 'classId',
            editor: new Ext.form.ComboBox({
                triggerAction: 'all',
                editable: false,
                valueField: 'id',
                displayField: 'text',
                store: pimcore.globalmanager.get("object_types_store")
            }),
            renderer: function (value) {
                var store = pimcore.globalmanager.get("object_types_store");
                var classObject = store.getById(value);
                if (classObject) {
                    return classObject.data.text;
                }
            }
        });

        gridColumns.push({
            hideable: false,
            xtype: 'actioncolumn',
            width: 30,
            items: [
                {
                    tooltip: t("web2print_web2print_favourite_output_channel_configuration"),
                    iconCls: "bundle_outputdataconfig_icon",
                    handler: function (grid, rowIndex) {

                        var data = grid.getStore().getAt(rowIndex);

                        if (empty(data.data.classId)) {
                            Ext.MessageBox.alert(t("error"), t("web2print_web2print_favourite_output_channel_select_class"));
                            return;
                        }

                        var channel = {
                            id: "SOME-ID",
                            channel: Ext.util.Format.htmlEncode(data.data.description),
                            classId: data.data.classId,
                            configuration: Ext.decode(data.data.configuration)
                        };

                        var dialog = new pimcore.bundle.outputDataConfigToolkit.OutputDataConfigDialog(
                            channel,
                            this.saveConfigDialog.bind(this, grid, rowIndex)
                        );

                    }.bind(this)
                }
            ]
        });

        gridColumns.push({
            hideable: false,
            xtype: 'actioncolumn',
            width: 40,
            items: [
                {
                    tooltip: t('remove'),
                    icon: "/bundles/pimcoreadmin/img/flat-color-icons/delete.svg",
                    handler: function (grid, rowIndex) {
                        grid.getStore().removeAt(rowIndex);
                    }.bind(this)
                }
            ]
        });

        this.grid = Ext.create('Ext.grid.Panel', {
            frame: false,
            store: this.store,
            border: true,
            columns: gridColumns,
            loadMask: true,
            bodyCls: "pimcore_editable_grid",
            stripeRows: true,
            trackMouseOver: true,
            viewConfig: {
                forceFit: false
            },
            plugins: [
                Ext.create('Ext.grid.plugin.CellEditing', {
                    clicksToEdit: 1
                }),
                'pimcore.gridfilters'
            ],
            selModel: Ext.create('Ext.selection.RowModel', {}),
            bbar: this.pagingtoolbar,
            tbar: [
                {
                    text: t('add'),
                    handler: this.onAdd.bind(this),
                    iconCls: "pimcore_icon_add"
                }
            ]
        });

        this.store.load();

        return this.grid;
    },

    onAdd: function (btn, ev) {
        var u = {};
        this.grid.store.insert(0, u);
    },

    saveConfigDialog: function (grid, rowIndex, configData) {

        var data = grid.getStore().getAt(rowIndex);
        Ext.Ajax.request({
            url: '/admin/outputdataconfig/admin/get-attribute-labels',
            method: 'POST',
            params: {
                classId: data.data.classId,
                configuration: Ext.encode(configData.config)
            },
            success: function (response) {
                var responseObject = Ext.decode(response.responseText);
                data.set("configuration", Ext.encode(responseObject.configuration));
            }.bind(this)
        });
    }

});

