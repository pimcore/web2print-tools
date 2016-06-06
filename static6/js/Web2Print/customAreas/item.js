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


pimcore.registerNS("pimcore.plugin.Web2Print.customAreas.item");
pimcore.plugin.Web2Print.customAreas.item = Class.create({

    parent: {},
    allowedOperators: ['Concatenator',/*'Group',*/'Text'],
    outputChannelName: "web2print_customarea",
    editors : [],

    /**
     * constructor
     * @param parent
     * @param data
     */
    initialize: function(parent, data) {
        this.parent = parent;
        this.data = data;

        if(this.data.outputChannel && this.data.outputChannel.configuration) {
            this.data.outputChannel.configuration = Ext.decode(this.data.outputChannel.configuration);
        }

        this.tabPanel = new Ext.TabPanel({
            activeTab: 0,
            iconCls: "plugin_web2print_custom_areas plugin_web2print_custom_areas_overlay",
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            id: "pimcore_targeting_panel_" + this.data.id,
            buttons: [{
                text: t("save"),
                iconCls: "pimcore_icon_apply",
                handler: this.save.bind(this)
            }],
            items: [
                this.getSettings(),
                this.getEditorPanel("frontend.php", "frontend_php", "php"),
                this.getEditorPanel("frontend.css", "frontend_css", "css"),
                this.getEditorPanel("editmode.php", "editmode_php", "php"),
                this.getEditorPanel("editmode.css", "editmode_css", "css")
            ]
        });


        var panel = this.parent.getTabPanel();
        panel.add(this.tabPanel);
        panel.setActiveItem(this.tabPanel);
    },

    /**
     * Basic custom area Settings
     * @returns Ext.form.FormPanel
     */
    getSettings: function () {
        this.configSelector = new Ext.form.ComboBox({
            xtype: "combo",
            store: new Ext.data.JsonStore({
                proxy: {
                    url: '/plugin/Web2Print/admin/favorite-output-definitions',
                    type: 'ajax',
                    reader: {
                        type: 'json',
                        rootProperty: "data",
                        idProperty: 'id'
                    },
                    extraParams: {classId: this.getCurrentClassId()}
                },
                fields: ['id', 'description', 'configuration']
            }),
            valueField: 'id',
            displayField: 'description',
            triggerAction: "all",
            forceSelection: true
        });


        this.loadConfigButton = new Ext.Button({
            text: t("web2print_area_load"),
            disabled: !this.data.classId,
            style: "margin-left: 6px; margin-right: 6px",
            iconCls: "pimcore_icon_reload",
            handler: this.loadConfig.bind(this)
        });

        this.currentConfigLabel = new Ext.form.Label({
            text: t("web2print_outputchanneltable_last_loaded_favorite") + ": " + this.data.selectedFavouriteOutputChannel,
            style: "padding: 8px 8px 6px 0"
        });


        this.editOutputChannelButton = new Ext.Button({
            text: t("web2print_area_edit_outputchannel"),
            disabled: !this.data.classId,
            style: "margin-top: 10px; float:left",
            iconCls: "plugin_outputdataconfig_icon plugin_outputdataconfig_icon_overlay",
            handler: this.openConfigDialog.bind(this)
        });


        this.settingsForm = new Ext.form.FormPanel({
            iconCls: "pimcore_icon_settings",
            title: t("settings"),
            bodyStyle: "padding:10px;",
            autoScroll: true,
            border:false,
            items: [{
                xtype: "textfield",
                name: "name",
                fieldLabel: t("name"),
                width: 500,
                value: this.data.name
            },{
                xtype: "textarea",
                name: "description",
                fieldLabel: t("description"),
                width: 500,
                height: 100,
                value: this.data.description
            },{
                xtype: "textfield",
                name: "type",
                fieldLabel: t("type"),
                width: 500,
                value: this.data.type
            },{
                xtype: "checkbox",
                name: "active",
                fieldLabel: t("active"),
                checked: this.data.active == "1"
            },{
                name: "classId",
                fieldLabel: t("class"),
                xtype: "combo",
                width: 500,
                store: pimcore.globalmanager.get("object_types_store"),
                mode: "local",
                editable: false,
                displayField: 'text',
                valueField: 'id',
                value: this.data.classId,
                triggerAction: "all",
                listeners: {
                    select: this.changeClass.bind(this)
                }
            },{
                xtype: 'fieldset',
                layout: "hbox",
                fieldLabel: t("web2print_area_outputchannel"),
                style: "margin-top: 10px; border: none !important; padding-left: 0",
                items: [
                    this.configSelector,
                    this.loadConfigButton,
                    this.currentConfigLabel
                ]
            },this.editOutputChannelButton]
        });

        return this.settingsForm;
    },

    getCurrentClassId: function() {
        return this.data.classId;
    },

    changeClass: function(value) {
        this.data.classId = value.getValue();
        this.data.outputChannel = null;
        this.configSelector.setDisabled(false);

        var proxy = this.configSelector.getStore().getProxy();
        proxy.extraParams.classId = this.getCurrentClassId();
        this.configSelector.getStore().load({params: {classId: this.getCurrentClassId()}});
        this.loadConfigButton.setDisabled(false);
        this.editOutputChannelButton.setDisabled(false);
        this.createOrGetOutputChannel();
    },

    loadConfig: function() {
        var store = this.configSelector.store;
        var entry = store.getById(this.configSelector.getValue());
        if(entry) {
            var outputChannel = this.createOrGetOutputChannel();
            var config = Ext.decode(entry.data.configuration);
            outputChannel.configuration = config;
            this.updateSelectedFavouriteOutputChannelLabel(entry.data.description);
            pimcore.helpers.showNotification(t('web2print_outputchanneltable_load_favorite'), t('web2print_outputchanneltable_load_favorite_success'), "success");
        }
    },

    openConfigDialog: function() {
        var outputChannel = this.createOrGetOutputChannel();
        var dialog = new pimcore.plugin.outputDataConfigToolkit.OutputDataConfigDialog(outputChannel, this.saveConfigDialog.bind(this), this.allowedOperators);
    },

    updateSelectedFavouriteOutputChannelLabel: function(description) {
        this.currentConfigLabel.setText(t("web2print_outputchanneltable_last_loaded_favorite") + ": " + description);
        this.data.selectedFavouriteOutputChannel = description;
    },

    saveConfigDialog: function(data) {
        this.data.outputChannel.id = data.id;
        var oldConfigString = Ext.encode(this.data.outputChannel.configuration);
        this.data.outputChannel.configuration = data.config;

        this.configSelector.setValue("");

        Ext.Ajax.request({
            url: '/plugin/Elements_OutputDataConfigToolkit/admin/get-attribute-labels',
            method: 'POST',
            params: {
                classId: this.data.outputChannel.o_classId,
                configuration: Ext.encode(data.config)
            },
            success: function(response) {
                var data = Ext.decode(response.responseText);

                if(oldConfigString != Ext.encode(data.configuration)) {
                    this.updateSelectedFavouriteOutputChannelLabel("");
                }

                this.data.outputChannel.configuration = data.configuration;
            }.bind(this)
        });
    },

    createOrGetOutputChannel: function() {
        if(!this.data.outputChannel) {
            this.data.outputChannel = {
                channel: this.outputChannelName + "_" + this.data.id,
                o_classId: this.getCurrentClassId(),
                configuration: []
            };
        }
        return this.data.outputChannel;
    },

    getEditorPanel: function(title, content, type) {

        var textarea = new Ext.form.TextArea({
            value: this.data[content],
            style: "font-family:courier"
        });

        var cls = "plugin_web2print_customarea_file_css";
        if(type == "php") {
            cls = "plugin_web2print_customarea_file_php";
        }

        var editor = new Ext.Panel({
            title: title,
            closable: false,
            layout: "fit",
            iconCls: cls,
            bodyStyle: "position:relative;",
            items: [textarea]
        });

        this.editors[content] = textarea;

        return editor;
    },


    /**
     * save config
     * @todo
     */
    save: function () {
        var saveData = {};

        // general settings
        saveData["settings"] = this.settingsForm.getForm().getFieldValues();

        saveData["editmode_php"] = this.editors["editmode_php"].getValue();
        saveData["editmode_css"] = this.editors["editmode_css"].getValue();
        saveData["frontend_php"] = this.editors["frontend_php"].getValue();
        saveData["frontend_css"] = this.editors["frontend_css"].getValue();

        // send data
        Ext.Ajax.request({
            url: "/plugin/Web2Print/Custom-Area/save",
            params: {
                id: this.data.id,
                data: Ext.encode(saveData),
                outputChannel: Ext.encode(this.data.outputChannel),
                selectedFavouriteOutputChannel: this.data.selectedFavouriteOutputChannel
            },
            method: "post",
            success: this.saveOnComplete.bind(this)
        });
    },

    /**
     * saved
     */
    saveOnComplete: function (response) {
        var res = Ext.decode(response.responseText);
        if(res.success) {
            this.parent.tree.getStore().reload();
            pimcore.helpers.showNotification(t("success"), t("plugin_web2pint_custom_area_saved_successfully"), "success");
        }
    }

});

