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


pimcore.registerNS("pimcore.document.editables.metaentry.table");
pimcore.document.editables.metaentry.table = Class.create(pimcore.document.editables.metaentry.abstract, {

    subtype: "table",

    openDialog: function(record) {
        var nameText = new Ext.form.TextField({
            name: "name",
            fieldLabel: t('web2print_outputchanneltable_name'),
            length: 255,
            width: 400,
            value: record.data.path
        });

        this.valueStore = new Ext.data.JsonStore({
            fields: ["value", "span"],
            data: record.data.config ? record.data.config.values : []
        });

        var valueGrid = Ext.create('Ext.grid.Panel', {
            bodyCls: "pimcore_editable_grid",
            tbar: [{
                xtype: "tbtext",
                text: t("web2print_outputchanneltable_values")
            }, "-", {
                xtype: "button",
                iconCls: "pimcore_icon_add",
                handler: function () {
                    var u = {
                        value: "",
                        span: 1
                    };
                    this.valueStore.insert(0, u);
                }.bind(this)
            }],
            plugins: [
                Ext.create('Ext.grid.plugin.CellEditing', {
                    clicksToEdit: 1
                })
            ],
            viewConfig: {
                plugins: {
                    ptype: 'gridviewdragdrop',
                    dragroup: 'element'
                }
            },
            style: "margin-top: 10px",
            store: this.valueStore,
            width: "100%",
            selModel: Ext.create('Ext.selection.RowModel', {}),
            columnLines: true,
            columns: [
                {header: t("web2print_outputchanneltable_value"), sortable: false, dataIndex: 'value', editor: new Ext.form.TextField({}), flex: 320},
                {header: t("web2print_outputchanneltable_span"), sortable: false, dataIndex: 'span', editor: new Ext.form.NumberField({}), width: 180},
                {
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
                }
            ],
            autoHeight: true
        });

        var configPanel = new Ext.form.FormPanel({
            bodyStyle: "padding: 10px;",
            autoScroll: true,
            items: [nameText, valueGrid],
            buttons: [{
                text: t("apply"),
                iconCls: "pimcore_icon_apply",
                handler: function () {
                    this.updateMetaEntry(record, configPanel.getForm().getFieldValues());
                }.bind(this)
            }]
        });

        this.metaEntryWindow = new Ext.Window({
            width: 650,
            height: 300,
            modal: true,
            title: t('web2print_outputchanneltable_metaentry'),
            layout: "fit",
            items: [configPanel]
        });

        this.metaEntryWindow.show();
    },

    updateMetaEntry: function(record, values) {
        record.set("path", values.name);


        var options = [];
        this.valueStore.commitChanges();
        this.valueStore.each(function (rec) {
            options.push({
                value: rec.get("value"),
                span: rec.get("span")
            });
        });

        record.set("config", {'values': options});
        this.metaEntryWindow.close();
    }

});
