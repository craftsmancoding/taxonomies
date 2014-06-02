function renderProductContainer(isProductContainerCreate, config){
    console.debug('[Moxycart renderProductContainer] is product create? : ', isProductContainerCreate);
	var tabPanel = Ext.getCmp("modx-resource-tabs");
	
	if(tabPanel==null){
	   alert('Moxycart: Error loading Javascripts for parent resource. Store cannot load!');
	   return;
	}
	//Add store setting tab
	var storeSettingsTab = {
		title: 'Store Settings',
		//xtype : 'form',
		id: 'modx-resource-StoreSettings',
		cls: 'modx-resource-tab',
		layout: 'form',
		labelAlign: 'top',
		labelSeparator: '',
		bodyCssClass: 'tab-panel-wrapper main-wrapper',
		autoHeight: true,
		defaults: {
			border: false,
			msgTarget: 'under',
			width: 400
		},
		items: getStoreSettingsFields(config)
	};

	tabPanel.insert(0, storeSettingsTab);

	//Add products tab
	var prductsTab = {
		title: 'Products',
		id: 'modx-resource-Products',
		cls: 'modx-resource-tab',
		layout: 'fit',
		labelAlign: 'top',
		labelSeparator: '',
		bodyCssClass: 'tab-panel-wrapper main-wrapper',
		autoHeight: true,
		defaults: {
			border: false,
			msgTarget: 'under',
			width: 400,
			height:'auto'
		},
		items: [
            {
                xtype: "box",
                autoEl: {cn: '<div id="store_products"></div>'}
            }
		]
	};
    tabPanel.on('tabchange', function(parent,selectedTab){ 
        if (selectedTab.id == 'modx-resource-Products') {
            Ext.getCmp("modx-resource-content").hide();
        }
        else {
            Ext.getCmp("modx-resource-content").show();
        }
    });
	tabPanel.insert(0, prductsTab);
	tabPanel.setActiveTab(0);
	tabPanel.doLayout();
}






function getFields(container){
    //container.add({html:"<p>Sample Fields...</p>"});
/*
	Ext.Ajax.request({
		url: connector_url+'json_specs&t=data',
		success: function(response, opts) {
			var obj = Ext.decode(response.responseText);
			if(obj && obj.results){
				for(n in obj.results){
					if(obj.results[n].spec_id) container.add({ xtype : 'checkbox', name : 'specs_'+obj.results[n].spec_id, checked : activeRecord.specs[obj.results[n].spec_id] ? true : false, boxLabel : obj.results[n].name, listeners : { check : function(){Ext.getCmp('modx-abtn-save').enable(true)} } });
				}
			}
		}
	});
*/
}

function getTaxonomies(container){
/*
	Ext.Ajax.request({
		url: connector_url+'json_store_taxonomies&t=data',
		params : {
			store_id : pid
		},
		method : 'GET',
		success: function(response, opts) {
			if(response.responseText === 'Invalid store. Include valid store_id'){
				return;
			}
			var obj = Ext.decode(response.responseText);
			if(obj && obj.results){
				for(n in obj.results){
					if(obj.results[n].id) {
						container.add({ xtype : 'checkbox', name : 'taxonomies_'+obj.results[n].id, checked : activeRecord.taxonomies[obj.results[n].id] ? true : false, boxLabel : obj.results[n].pagetitle, listeners : { check : function(){Ext.getCmp('modx-abtn-save').enable(true)} } });
					}
				}
			}
		}
	});
*/
}


function getTemplateStore(dtmp){
	var store = Ext.StoreMgr.get('defaultTemplate');
	if(store) return store;
	else return new Ext.data.Store({
		fields: ['id', 'name'],
		autoLoad : true,
		storeId : 'defaultTemplates',
		reader : new Ext.data.JsonReader({
			idProperty: 'id',
			root: 'results',
			totalProperty: 'total',
			fields: [
				{name: 'id'},
				{name: 'name'}
			]
		}),
		proxy : new Ext.data.HttpProxy({
			method: 'GET',
			prettyUrls: false,
			url: connector_url+'json_templates&t=data'
		}),
		listeners : {
			load : function(){
				var dt =  Ext.getCmp('defaultTemplates');
				if(dt) {
					if(dtmp || typeof default_template !== "undefined") dt.setValue(dtmp || default_template);
					//else dt.setValue(this.items.items[0].data.);
				}
			}
		}
	});
}



function getStoreSettingsFields(config){
	MODx.activePage.config.record.properties = MODx.activePage.config.record.properties || {};
	activeRecord = MODx.activePage.config.record.properties.moxycart || {};
	getTemplateStore(activeRecord.product_template);
	config = config || {record:{}};

	return [{
		anchor: '100%',
		border:false,
		monitorValid:true,
		layout:'form',
		id: 'modx-resource-storesettings-columns',
		style: 'border:0px',
		listeners : {
			clientvalidation: function(){
				console.log('validate');
			}
		},
		defaults: {
			labelSeparator: '',
			labelAlign: 'top',
			border: false,
			msgTarget: 'under',
			bodyCssClass:'pnl-mo-border'
		},
		items:[{
			border:false,
			frame:true,
			layout:'table',
			width:'100%',
			style: 'border:0px',
			layoutConfig:{
				tableAttrs:{
					cellspacing:8
				},
				columns:4
			},
			defaults : {
				enableKeyEvents : true,
				listeners : {
					keypress : function(){
						Ext.getCmp('modx-abtn-save').enable(true);
					},
					change : function(){
						Ext.getCmp('modx-abtn-save').enable(true);
					},
					select : function(){
						Ext.getCmp('modx-abtn-save').enable(true);
					}
				}
			},
			items:[
				{
					width:'100'
				},
				{
					width:'200'
				},
				{
					width:'100'
				},
				{
					width:'100'
				},
				{
					xtype: 'label',
					text: 'Product Type'
				},{
					xtype : 'combo',
					fieldLabel: 'Product Type',
					name : 'product_type',
					// This is needed to send the proper value
					hiddenName: 'product_type',
					editable: false,
					triggerAction: 'all',
					mode: 'local',
					typeAhead: true,
					width : 280,
					displayField:'name',
					valueField:'value',
					value : activeRecord.product_type,
					store:new Ext.data.ArrayStore({
						autoDestroy: true,
						fields: [
						   {name: 'name'},
						   {name: 'value'}
						],
						data:[['Regular', 'regular'], ['Subscription', 'subscription'], ['Download', 'download']]
					})
				},
				{
					colspan:2
				},
				{
					xtype: 'label',
					text: 'Default template'
				}, {
					xtype : 'combo',
					name : 'product_template',
					fieldLabel: 'Default Template',
					editable: true,
					id : 'defaultTemplates',
					mode : 'remote',
					pageSize : 10,
					width : 280,
					typeAhead: false,
					triggerAction: 'all',
					lastQuery: '',
					displayField : 'name',
					valueField : 'id',
					// value: 7, // <-- need to set this so the combobox repopulates!
					// See http://docs.sencha.com/extjs/3.4.0/#!/api/Ext.form.ComboBox
					hiddenName: 'product_template',
					store: 'defaultTemplates'
				},{
					colspan:2
				},
				{
					xtype: 'label',
					text: 'Track Inventory'
				},{
					xtype : 'combo',
					name : 'track_inventory',
					fieldLabel: 'Track Inventory',
					editable: false,
					triggerAction: 'all',
					mode: 'local',
					typeAhead: true,
					width : 280,
					displayField:'name',
					valueField:'value',
					value : activeRecord.track_inventory,
//					hiddenName: 'track_inventory',
					store:new Ext.data.ArrayStore({
						autoDestroy: true,
						fields: [
							{name: 'name'},
							{name: 'value'}
						],
						data:[['Yes', 1], ['No', 0]]
//						data:[{name:'Yes', value:1}, {name:'No', value:0}]
					})
				},
				{
					colspan:2
				},
				{
					xtype: 'label',
					text: 'Default Sort Order'
				},{
					xtype : 'combo',
					name : 'sort_order',
					fieldLabel: 'Default Sort Order',
					editable: false,
					triggerAction: 'all',
					mode: 'local',
					typeAhead: true,
					width : 280,
					displayField:'name',
					valueField:'value',
					value:activeRecord.sort_order,
					store:new Ext.data.ArrayStore({
						autoDestroy: true,
						fields: [
							{name: 'name'},
							{name: 'value'}
						],
						data:[['Product Name', 'name'], ['SKU', 'sku'], ['Last Modified', 'timestamp_modified'], ['Manual Sort Order', 'seq']]
					})
				},
				{
					colspan:2
				},
				{
					xtype: 'label',
					text: 'Default Inventory Alert Level'
				},
				{
					xtype : 'textfield',
					name : 'qty_alert',
					value : activeRecord.qty_alert,
					fieldLabel: 'Default Inventory Alert Level',
					width : 280
				},
				{
					colspan:2
				},
				{
					colspan:4,
					height:10
				},
				{
					xtype: 'label',
					text: 'Custom Fields',
					style: 'margin-top:20px',
					ctCls:'v-align-top'
				},
				{
					layout:'column',
					border:false,
					columns:4,
					items:[{
						layout:'fit',
						border: false,
						listeners : {
							afterrender : function(f){
								getFields(f);
							}
						}
					}]
				},
				{
					xtype: 'label',
					text: 'Taxonomies',
					ctCls:'v-align-top'
				},
				{
					layout:'column',
					border:false,
					columns:4,
					items:[{
						layout:'fit',
						border: false,
						listeners : {
							afterrender : function(f){
								getTaxonomies(f);
							}
						}
					}]
				},{
					colspan:4,
					height:10
				}]
			}]
	}];
}




var triggerDirtyField = function(fld) {
	Ext.getCmp('modx-panel-resource').fieldChangeEvent(fld);
};
MODx.triggerRTEOnChange = function() {
	triggerDirtyField(Ext.getCmp('textProduct'));
};
MODx.fireResourceFormChange = function(f,nv,ov) {
	Ext.getCmp('modx-panel-resource').fireEvent('fieldChange');
};
