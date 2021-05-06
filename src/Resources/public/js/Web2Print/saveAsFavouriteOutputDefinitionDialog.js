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


pimcore.registerNS("pimcore.bundle.web2print.SaveAsFavouriteOutputDefinitionDialog");
pimcore.bundle.web2print.SaveAsFavouriteOutputDefinitionDialog = Class.create({


    initialize: function (currentClassId, callback) {

        var nameText = new Ext.form.TextField({
            name: "text",
            length: 255,
            width: 200
        });

        var configSelector = new Ext.form.ComboBox({
            name: "existing",
            width: 200,
            disabled: true,
            store: new Ext.data.JsonStore({
                proxy: {
                    url: '/admin/web2printtools/admin/favorite-output-definitions',
                    type: 'ajax',
                    reader: {
                        type: 'json',
                        rootProperty: "data",
                        idProperty: 'id'
                    },
                    extraParams: {classId: currentClassId}
                },
                fields: ['id', 'description']
            }),
            valueField: 'id',
            displayField: 'description',
            triggerAction: "all",
            forceSelection: true
        });


        var radioNew = new Ext.form.Radio({
            name: "selection",
            checked: true,
            value: "new",
            listeners: {
                change: function(element, checked) {
                    nameText.setDisabled(!checked);
                    configSelector.setDisabled(checked);
                }
            }
        });

        var compositeNew = {
            xtype: 'fieldcontainer',
            layout: 'hbox',
            hideLabel: true,
            items: [
                radioNew,
                {xtype: 'label', text: t("web2print_outputchanneltable_save_favorite_name"), width: 150, style: 'margin-top: 6px; margin-left: 25px'},
                nameText
            ]
        };

        var radioExisting = new Ext.form.Radio({
            name: "selection",
            value: "existing",
            listeners: {
                change: function(element, checked) {
                    nameText.setDisabled(checked);
                    configSelector.setDisabled(!checked);
                }
            }
        });

        var compositeExisting = {
            xtype: 'fieldcontainer',
            layout: 'hbox',
            hideLabel: true,
            items: [
                radioExisting,
                {xtype: 'label', text: t("web2print_outputchanneltable_overwrite_favorite"), width: 150, style: 'margin-top: 6px; margin-left: 25px'},
                configSelector
            ]
        };

        var configPanel = new Ext.form.FormPanel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            labelWidth: 0,
            items: [compositeNew, compositeExisting],
            buttons: [{
                text: t("apply"),
                iconCls: "pimcore_icon_apply",
                handler: function() {
                    callback(configPanel.getForm().getFieldValues());
                }.bind(this)
            }]
        });

        this.dialog = new window.parent.Ext.Window({
            width: 450,
            height: 200,
            modal: true,
            title: t('web2print_outputchanneltable_save_favorite'),
            layout: "fit",
            items: [configPanel]
        });

        this.dialog.show();
    },

    close: function() {
        this.dialog.close();
    }
});