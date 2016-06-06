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


pimcore.registerNS("pimcore.document.tags.metaentry.table");
pimcore.document.tags.metaentry.table = Class.create(pimcore.document.tags.metaentry.abstract, {

    subtype: "table",

    openDialog: function(record) {
        var nameText = new Ext.form.TextField({
            name: "name",
            fieldLabel: t('web2print_outputchanneltable_name'),
            length: 255,
            width: 200,
            value: record.data.path
        });

        this.valueStore = new Ext.data.JsonStore({
            fields: ["value", "span"],
            data: record.data.config ? record.data.config.values : []
        });

        var valueGrid = new Ext.grid.EditorGridPanel({
            tbar: [{
                xtype: "tbtext",
                text: t("web2print_outputchanneltable_values")
            }, "-", {
                xtype: "button",
                iconCls: "pimcore_icon_add",
                handler: function () {
                    var u = new this.valueStore.recordType({
                        value: "",
                        span: 1
                    });
                    this.valueStore.insert(0, u);
                }.bind(this)
            }],
            style: "margin-top: 10px",
            store: this.valueStore,
            width: 400,
            plugins: [new Ext.ux.dd.GridDragDropRowOrder({})],
            selModel:new Ext.grid.RowSelectionModel({singleSelect:true}),
            columnLines: true,
            columns: [
                {header: t("web2print_outputchanneltable_value"), sortable: false, dataIndex: 'value', editor: new Ext.form.TextField({}), width: 320},
                {header: t("web2print_outputchanneltable_span"), sortable: false, dataIndex: 'span', editor: new Ext.form.NumberField({}), width: 40},
                {
                    xtype: 'actioncolumn',
                    width: 30,
                    items: [
                        {
                            tooltip: t('remove'),
                            icon: "/pimcore/static/img/icon/cross.png",
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
            layout: "form",
            bodyStyle: "padding: 10px;",
            labelWidth: 100,
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
            width: 450,
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