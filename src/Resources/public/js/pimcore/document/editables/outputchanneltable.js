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


pimcore.registerNS("pimcore.document.editables.outputchanneltable");
pimcore.document.editables.outputchanneltable = Class.create(pimcore.document.editable, {

    selectedClass: null,
    selectedFavouriteOutputChannel: null,
    outputChannel: null,
    documentId: null,
    outputChannelName: null,
    allowedOperators: ['Concatenator','Group','Text'],


    initialize: function(id, name, options, data, inherited) {

        this.id = id;
        this.name = name;

        if (!options) {
            options = {};
        }


        this.options = options;
        this.data = data.elements;

        if(options.selectedClass){
            this.selectedClass = options.selectedClass;
        }else{
            this.selectedClass = data.selectedClass;
        }

        this.selectedFavouriteOutputChannel = data.selectedFavouriteOutputChannel ? data.selectedFavouriteOutputChannel : "";
        this.documentId = data.documentId;
        this.outputChannelName = "web2print_" + this.name;

        if(data.outputChannel) {
            this.outputChannel = Ext.decode(data.outputChannel);
        }

        this.setupWrapper();


        this.store = new Ext.data.ArrayStore({
            data: this.data,
            fields: [
                "id",
                "path",
                "type",
                "subtype",
                "config"
            ],
            listeners: {
                'add': function(store, records, index) {
                    var record = store.getAt(index);
                    if(record.data.type == "meta" && record.data.newRecord == true) {
                        this.openMetaInfoDialog(record);
                        record.data.newRecord = false;
                    }

                }.bind(this)
            }
        });


        var elementConfig = {
            disabled: !this.selectedClass,
            store: this.store,
            selModel: Ext.create('Ext.selection.RowModel', {}),
            viewConfig: {
                plugins: {
                    ptype: 'gridviewdragdrop',
                    dragroup: 'element'
                },
                listeners: {
                    drop: function(node, data, dropRec, dropPosition) {
                        var dropOn = dropRec ? ' ' + dropPosition + ' ' + dropRec.get('name') : ' on empty view';
                        //Ext.example.msg('Drag from left to right', 'Dropped ' + data.records[0].get('name') + dropOn);
                    }
                }
            },
            border: false,
            cls: "outputchanneltable",
            frame: true,
            columns: {
                defaults: {
                    sortable: false
                },
                items: [
                    {header: 'ID', dataIndex: 'id', width: 50},
                    {header: t("path"), dataIndex: 'path', flex: 250},
                    {header: t("type"), dataIndex: 'type', width: 100},
                    {header: t("subtype"), dataIndex: 'subtype', width: 100},
                    {
                        xtype: 'actioncolumn',
                        width: 40,
                        items: [{
                            tooltip: t('open'),
                            icon: "/bundles/pimcoreadmin/img/flat-color-icons/cursor.svg",
                            handler: function (grid, rowIndex) {
                                var data = grid.getStore().getAt(rowIndex);

                                if(data.data.type == "meta") {
                                    this.openMetaInfoDialog(data);
                                } else {
                                    var subtype = data.data.subtype;
                                    if (data.data.type == "object" && data.data.subtype != "folder") {
                                        subtype = "object";
                                    }
                                    pimcore.helpers.openElement(data.data.id, data.data.type, subtype);
                                }
                            }.bind(this)
                        }]
                    },
                    {
                        xtype: 'actioncolumn',
                        width: 40,
                        items: [{
                            tooltip: t('remove'),
                            icon: "/bundles/pimcoreadmin/img/flat-color-icons/delete.svg",
                            handler: function (grid, rowIndex) {
                                grid.getStore().removeAt(rowIndex);
                            }.bind(this)
                        }]
                    }
                ]
            },
            tbar: {
                items: [
                    {
                        xtype: "tbspacer",
                        width: 20,
                        height: 16,
                        cls: "pimcore_icon_droptarget"
                    },
                    {
                        xtype: "tbtext",
                        text: "<b>" + (this.options.title ? this.options.title : "") + "</b>"
                    },
                    "->",
                    this.getAddMetaInfoControl(),
                    {
                        xtype: "button",
                        iconCls: "pimcore_icon_delete",
                        handler: this.empty.bind(this)
                    },
                    {
                        xtype: "button",
                        iconCls: "pimcore_icon_search",
                        handler: this.openSearchEditor.bind(this)
                    }
                ]
            },

            ddGroup: 'element'
        };

        // height specifics
        if(typeof this.options.height != "undefined") {
            elementConfig.height = this.options.height;
        } else {
            elementConfig.autoHeight = true;
        }

        // width specifics
        if(typeof this.options.width != "undefined") {
            elementConfig.width = this.options.width;
        }

        this.gridElement = Ext.create("Ext.grid.Panel", elementConfig);

        this.gridElement.on("rowcontextmenu", this.onRowContextmenu.bind(this));

        this.gridElement.on("afterrender", function (el) {
            // register at global DnD manager
            dndManager.addDropTarget(this.gridElement.getEl(),
                this.onNodeOver.bind(this),
                this.onNodeDrop.bind(this)
            );

        }.bind(this));

        var classStore = pimcore.globalmanager.get("object_types_store");
        var possibleClasses = [];
        classStore.each(function (rec) {
            possibleClasses.push([rec.data.text, rec.data.translatedText]);
        });

        this.classSelector = new Ext.form.ComboBox({
            xtype: "combo",
            store: possibleClasses,
            mode: "local",
            name: "class",
            cls : "web2print-outputchannel-class-selector",
            triggerAction: "all",
            forceSelection: true,
            value: this.selectedClass,
            fieldLabel: t("class"),
            style: 'margin-bottom: 10px',
            listeners: {
                select: this.changeClass.bind(this)
            }
        });

        this.configSelector = new Ext.form.ComboBox({
            xtype: "combo",
            store: new Ext.data.JsonStore({
                proxy: {
                    url: '/admin/web2printtools/admin/favorite-output-definitions',
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
            disabled: !this.selectedClass,
            iconCls: "pimcore_icon_reload",
            style: "margin-left: 6px; margin-right: 6px",
            handler: this.loadConfig.bind(this)
        });
        this.currentConfigLabel = new Ext.form.Label({
            text: t("web2print_outputchanneltable_last_loaded_favorite") + ": " + this.selectedFavouriteOutputChannel,
            style: "padding: 8px 8px 6px 0"
        });

        this.configSelection = {
            xtype: 'fieldset',
            layout: 'hbox',
            border: false,
            fieldLabel: t("web2print_area_outputchannel"),
            style: "margin-top: 10px; border: none !important; padding-left: 0",
            items: [
                this.configSelector,
                this.loadConfigButton,
                this.currentConfigLabel
            ]
        };

        this.editOutputChannelButton = new Ext.Button({
            text: t("web2print_area_edit_outputchannel"),
            disabled: !this.selectedClass,
            style: "margin-top: 10px; float:left;",
            iconCls: "bundle_outputdataconfig_icon",
            handler: this.openConfigDialog.bind(this)
        });


        this.saveOutputChannelButton = new Ext.Button({
            text: t("web2print_area_save_outputchannel"),
            disabled: !this.selectedClass,
            style: "margin: 10px 0 10px 10px;",
            iconCls: "pimcore_icon_publish",
            handler: this.saveFavoriteConfig.bind(this)
        });

        var items = this.getFormItems();
        this.element = new Ext.form.FormPanel({
            bodyStyle: "padding: 10px;",
            border: false,
            items: items
        });


        this.element.render(id);
    },

    getFormItems : function(){
        var items = [];
        if(!this.options.disableClassSelection){
            items.push(this.classSelector);
        }
        items.push(this.gridElement, this.editOutputChannelButton);

        if(!this.options.disableFavoriteOutputChannel){
            items.push(this.saveOutputChannelButton, this.configSelection)
        }

        return items;
    },

    getAddMetaInfoControl: function () {

        var typeMenu = [];
        for(var type in window.parent.pimcore.document.editables.metaentry) {
            if(type != "abstract") {
                typeMenu.push({
                    text: type,
                    handler: this.addMetaInfo.bind(this, type),
                    iconCls: "pimcore_icon_fieldcollections"
                });
            }
        }

        var items = [];

        if (typeMenu.length == 1) {
            items.push({
                cls: "pimcore_block_button_plus",
                iconCls: "pimcore_icon_plus",
                handler: typeMenu[0].handler
            });
        } else if (typeMenu.length > 1) {
            items.push({
                cls: "pimcore_block_button_plus",
                iconCls: "pimcore_icon_plus",
                menu: typeMenu
            });
        } else {
            items.push({
                xtype: "tbtext",
                text: t("no_collections_allowed")
            });
        }

        return items[0];
    },

    addMetaInfo: function(type) {

        var entry = new window.parent.pimcore.document.editables.metaentry[type]();
        var initData = entry.getInitData("meta"  + (this.store.getCount() + 1));
        initData.newRecord = true;

        // check for existing element
        if (!this.elementAlreadyExists(initData.id, initData.type)) {
            this.store.add(initData);
        }
    },

    openMetaInfoDialog: function(record) {
        var entry = new window.parent.pimcore.document.editables.metaentry[record.data.subtype]();
        entry.openDialog(record);
    },

    changeClass: function(value) {
        this.selectedClass = value.getValue();
        this.outputChannel = null;
        this.empty();
        this.gridElement.setDisabled(false);
        this.configSelector.setDisabled(false);

        var proxy = this.configSelector.store.getProxy();
        proxy.extraParams.classId = this.getCurrentClassId();

        this.configSelector.store.load({params: {classId: this.getCurrentClassId()}});
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

    saveFavoriteConfig: function() {
        this.saveFavoriteConfigDialog = new window.parent.pimcore.bundle.web2print.SaveAsFavouriteOutputDefinitionDialog(this.getCurrentClassId(), this.doSaveFavoriteConfig.bind(this));
    },

    doSaveFavoriteConfig: function(params, force) {
        Ext.Ajax.request({
            url: '/admin/web2printtools/admin/save-or-update-favorite-output-definition',
            method: 'POST',
            params: {
                text: params.text,
                existing: params.existing,
                classId: this.outputChannel.classId,
                configuration: Ext.encode(this.outputChannel.configuration),
                force: force ? true : ''
            },
            success: function(response) {
                var data = Ext.decode(response.responseText);
                if(data.success) {
                    this.updateSelectedFavouriteOutputChannelLabel(params.text);
                    pimcore.helpers.showNotification(t('web2print_outputchanneltable_save_favorite'), t('web2print_outputchanneltable_save_favorite_success'), "success");
                    this.saveFavoriteConfigDialog.close();
                } else if(data.nameexists) {
                    window.parent.Ext.MessageBox.confirm(t('web2print_outputchanneltable_save_favorite_name_exists'), t('web2print_outputchanneltable_overwrite_existing'), function(answer) {
                        if(answer == "yes") {
                            params.existing = data.id;
                            this.doSaveFavoriteConfig(params, force);
                        }
                    }.bind(this));
                } else {
                    pimcore.helpers.showNotification(t('web2print_outputchanneltable_save_favorite'), t('web2print_outputchanneltable_save_favorite_error'), "error");
                }
            }.bind(this)
        });


    },

    createOrGetOutputChannel: function() {
        if(!this.outputChannel) {
            this.outputChannel = {
                id: "SOME-ID",
                channel: this.outputChannelName,
                classId: this.getCurrentClassId(),
                configuration: []
            };
            this.saveOutputChannelButton.setDisabled(false);
        }
        return this.outputChannel;
    },

    openConfigDialog: function() {
        var outputChannel = this.createOrGetOutputChannel();
        var dialog = new window.parent.pimcore.bundle.outputDataConfigToolkit.OutputDataConfigDialog(outputChannel, this.saveConfigDialog.bind(this), this.allowedOperators);
    },

    saveConfigDialog: function(data) {
        this.outputChannel.id = data.id;
        var oldConfigString = Ext.encode(this.outputChannel.configuration);
        this.outputChannel.configuration = data.config;

        this.configSelector.setValue("");

        Ext.Ajax.request({
            url: '/admin/outputdataconfig/admin/get-attribute-labels',
            method: 'POST',
            params: {
                classId: this.outputChannel.classId,
                configuration: Ext.encode(data.config)
            },
            success: function(response) {
                var data = Ext.decode(response.responseText);

                if(oldConfigString != Ext.encode(data.configuration)) {
                    this.updateSelectedFavouriteOutputChannelLabel("");
                }

                this.outputChannel.configuration = data.configuration;
            }.bind(this)
        });
    },

    getCurrentClassId: function() {
        var classStore = pimcore.globalmanager.get("object_types_store");
        var index = classStore.find("text", this.selectedClass);
        if(typeof index !== 'undefined'  && classStore.getAt(index)) {
            return classStore.getAt(index).id;
        }
    },

    onNodeOver: function(target, dd, e, data) {
        var record = data.records[0];
        var data = record.data;

        if(data.elementType == "object" && data.className == this.selectedClass) {
            return Ext.dd.DropZone.prototype.dropAllowed;
        } else {
            return Ext.dd.DropZone.prototype.dropNotAllowed;
        }
    },

    onNodeDrop: function (target, dd, e, data) {
        var record = data.records[0];
        var data = record.data;

        //data = this.getCustomPimcoreDropData(data);
        if(data.elementType == "object" && data.className == this.selectedClass) {
            var initData = {
                id: data.id,
                path: data.path,
                type: data.elementType
            };

            if (initData.type == "object") {
                if (data.className) {
                    initData.subtype = data.className;
                }
                else {
                    initData.subtype = "folder";
                }
            }

            if (initData.type == "document" || initData.type == "asset") {
                initData.subtype = data.type;
            }

            // check for existing element
            if (!this.elementAlreadyExists(initData.id, initData.type)) {
                this.store.add(initData);
                return true;
            }
        }

        return false;

    },

    onRowContextmenu: function (grid, record, tr, rowIndex, e, eOpts) {

        var menu = new Ext.menu.Menu();
        var data = grid.getStore().getAt(rowIndex);

        menu.add(new Ext.menu.Item({
            text: t('remove'),
            iconCls: "pimcore_icon_delete",
            handler: this.removeElement.bind(this, rowIndex)
        }));

        menu.add(new Ext.menu.Item({
            text: t('open'),
            iconCls: "pimcore_icon_open",
            handler: function (data, item) {

                item.parentMenu.destroy();

                if(data.data.type == 'meta') {

                    this.openMetaInfoDialog(data);

                } else if(data.data.type == "object" ) {

                    var subtype = data.data.subtype;
                    if (data.data.type == "object" && data.data.subtype != "folder") {
                        subtype = "object";
                    }
                    pimcore.helpers.openElement(data.data.id, data.data.type, subtype);
                }

            }.bind(this, data)
        }));

        menu.add(new Ext.menu.Item({
            text: t('search'),
            iconCls: "pimcore_icon_search",
            handler: function (item) {
                item.parentMenu.destroy();
                this.openSearchEditor();
            }.bind(this)
        }));

        e.stopEvent();
        menu.showAt(e.pageX, e.pageY);
    },

    openSearchEditor: function () {

        var restrictions = {
            type: ["object"],
            subtype: {
                object: ["object", "folder", "variant"]
            },
            forceSubtypeFilter: true,
            specific: {
                classes: [this.selectedClass]
            }
        };
        pimcore.helpers.itemselector(true, this.addDataFromSelector.bind(this), restrictions);

    },

    elementAlreadyExists: function (id, type) {

        // check for existing element
        var result = this.store.queryBy(function (id, type, record, rid) {
            if (record.data.id == id && record.data.type == type) {
                return true;
            }
            return false;
        }.bind(this, id, type));

        if (result.length < 1) {
            return false;
        }
        return true;
    },

    addDataFromSelector: function (items) {
        if (items.length > 0) {
            for (var i = 0; i < items.length; i++) {
                if (!this.elementAlreadyExists(items[i].id, items[i].type)) {

                    var subtype = items[i].subtype;
                    if (items[i].type == "object") {
                        if (items[i].subtype == "object") {
                            if (items[i].classname) {
                                subtype = items[i].classname;
                            }
                        }
                    }

                    this.store.add({
                        id: items[i].id,
                        path: items[i].fullpath,
                        type: items[i].type,
                        subtype: subtype
                    });
                }
            }
        }
    },

    empty: function () {
        this.store.removeAll();
    },

    removeElement: function (index, item) {
        this.store.removeAt(index);
        item.parentMenu.destroy();
    },

    getValue: function () {
        var tmData = [];

        var data = this.store.queryBy(function(record, id) {
            return true;
        });


        for (var i = 0; i < data.items.length; i++) {
            tmData.push(data.items[i].data);
        }

        return {
            elements: tmData,
            outputChannel: Ext.encode(this.outputChannel),
            selectedClass: this.selectedClass,
            selectedFavouriteOutputChannel: this.selectedFavouriteOutputChannel
        };
    },

    updateSelectedFavouriteOutputChannelLabel: function(description) {
        this.currentConfigLabel.setText(t("web2print_outputchanneltable_last_loaded_favorite") + ": " + description);
        this.selectedFavouriteOutputChannel = description;
    },

    getType: function () {
        return "outputchanneltable";
    }
});
