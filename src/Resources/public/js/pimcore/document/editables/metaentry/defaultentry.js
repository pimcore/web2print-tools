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


pimcore.registerNS("pimcore.document.editables.metaentry.defaultentry");
pimcore.document.editables.metaentry.defaultentry = Class.create(pimcore.document.editables.metaentry.abstract, {

    subtype: "defaultentry",

    openDialog: function(record) {
        var nameText = new Ext.form.TextField({
            name: "name",
            fieldLabel: t('web2print_outputchanneltable_name'),
            length: 255,
            width: 200,
            value: record.data.path
        });
        var valueText = new Ext.form.TextArea({
            name: "value",
            fieldLabel: t('web2print_outputchanneltable_value'),
            length: 255,
            width: 200,
            height: 50,
            value: record.data.config ? record.data.config.value : ''
        });
        var spanCheck = new Ext.form.field.Checkbox({
            name: "span",
            boxLabel: t('web2print_outputchanneltable_span'),
            checked: record.data.config ? record.data.config.span : ''
        });


        var configPanel = new Ext.form.FormPanel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [nameText, valueText, spanCheck],
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
            height: 350,
            modal: true,
            title: t('web2print_outputchanneltable_metaentry'),
            layout: "fit",
            items: [configPanel]
        });

        this.metaEntryWindow.show();
    },

    updateMetaEntry: function(record, values) {
        record.set("path", values.name);
        record.set("config", {'value': values.value, 'span': values.span});
        this.metaEntryWindow.close();
    }

});