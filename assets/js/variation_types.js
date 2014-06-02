function renderVariationTypes(){

	var variationTypesStore = new Ext.data.Store({
		autoLoad:true,
		url: connector_url + 'json_variation_types&t=data',
		sortInfo:{
			field:'seq',
			direction: 'ASC'
		},		
		reader:new Ext.data.JsonReader({
			idProperty: 'vtype_id',
			root: 'results',
			totalProperty: 'total',
			fields:[
				{name: 'vtype_id'},
				{name: 'seq'},
				{name: 'name'},
				{name: 'description'},
				{name: 'terms'}				
			]
		})
	});

	var variationTypesContainer = new Ext.Panel({
		title:'Manage Variation Types',
		renderTo:'moxycart_canvas',
		layout:'border',
		width:'auto',
		height:500,		
		items:[
			{
				region:'center',
				xtype:'grid',
				id:'pnlVariationTypesGrid',
				store:variationTypesStore,
				layout:'fit',
				autoExpandColumn: 'name',
				selModel:new Ext.grid.RowSelectionModel({
					singleSelect:true
				}),				
				loadMask:true,
				enableDragDrop:true,
				ddGroup:'variationTypesDDGroup',
				enableHdMenu:false,				
				viewConfig: {
					forceFit: true,
					getRowClass:function(record, rowIndex, rp, ds){
						return 'moxycart-grid-row';
					}					
				},				
				cm:new Ext.grid.ColumnModel([
					  {
						header:'Name',
						dataIndex: 'name',
						width:80
					  },
					  {
						header: 'Description',
						dataIndex: 'description'
					  },
					  {
						header: 'Terms',
						dataIndex: 'terms'
					  },
					  {
						header:'',
						dataIndex: 'note',						
						align:'center',
						fixed:true,
						width:250,
						renderer:function(value, metaData, record, rowIndex, colIndex, store){
							var html = '<input type="button" value="Manage Terms" class="x-btn x-btn-noicon x-box-item" style="height:20px;width:90px;" onclick="return onManageTerms(' + record.get('vtype_id') + ');"/>';
							
							html += '&nbsp;&nbsp;';
							
							html += '<input type="button" value="Edit" class="x-btn x-btn-noicon x-box-item" style="height:20px;width:50px;"  onclick="return onVariationTypesEdit(' + record.get('vtype_id') + ');"/>';
							
							html += '&nbsp;&nbsp;';
							
							html += '<input type="button" value="Delete" class="x-btn x-btn-noicon x-box-item" style="height:20px;width:60px;"  onclick="return onVariationTypesDelete(' + record.get('vtype_id') + ');"/>';							
							
							return html;
						}
					  }
				]),
				listeners:{
					render:function(){

						var grid = Ext.getCmp('pnlVariationTypesGrid');
					
						var ddrow = new Ext.dd.DropTarget(grid.container, {
							ddGroup : 'variationTypesDDGroup',
							copy:false,
							notifyDrop : function(dd, e, data){

								var grid = Ext.getCmp('pnlVariationTypesGrid');
								var ds = grid.store;

								var sm = grid.getSelectionModel();
								var rows = sm.getSelections();

								if(dd.getDragData(e)) {
																
									var cindex=dd.getDragData(e).rowIndex;
									if(typeof(cindex) != "undefined") {
										for(i = 0; i <  rows.length; i++) {
											ds.remove(ds.getById(rows[i].id));
										}
										ds.insert(cindex,data.selections);
										sm.clearSelections();
										
										for(Icount=0;Icount<ds.getCount();Icount++){
										
											var record = ds.getAt(Icount);
										
											if(record.get('seq')!=Icount){												

												var values = {};
												Ext.applyIf(values, record.data);
												values.action = 'update';
												values.seq = Icount;
												
												
												Ext.Ajax.request({
												   url: connector_url + 'variation_type_save',
												   params:values,
												   success: function(response, options){					
												   },
												   failure: function(response, options){					
												   }
												});												
											}
											
										}				
									}
								}
							}
						}); 
						
					}
				}
			},
			{
				region:'north',
				height:60,
				xtype:'panel',
				bodyStyle:'padding:10px 0px 10px 30px;',
				border:false,
				layout:{
					type:'table',
					columns:2
				},				
				items:[
					{
						xtype:'button',
						text:'Create Variation Type',
						handler:function(){
							createUpdateVariationType(null);
						}
					}					
				]
			},
			{
				region:'south',
				height:30,
				border:false
			},
			{
				region:'east',
				width:30,
				border:false
			},
			{
				region:'west',
				width:30,
				border:false
			}			
		]
	});
	
}

function onManageTerms(vtype_id){
	MODx.loadPage(MODx.action['moxycart:index'], 'f=variation_terms_manage&vtype_id=' + vtype_id);
}

function onVariationTypesEdit(vtype_id){
	var pnlVariationTypesGrid = Ext.getCmp('pnlVariationTypesGrid');
	var selModel = pnlVariationTypesGrid.getSelectionModel();
	
	if(selModel!=null && selModel.hasSelection()){
		var record  = selModel.getSelected();
		createUpdateVariationType(record);
	}
	else{
		Ext.Msg.show({
		   title:'Error',
		   msg: 'Please select a row to edit variation type.',
		   buttons: Ext.Msg.OK,
		   icon: Ext.MessageBox.ERROR
		});		
	}
}

function onVariationTypesDelete(vtype_id){

	Ext.Msg.show({
	   title:'Confirm',
	   msg: 'Are you sure want to delete variation type?',
	   buttons: Ext.Msg.YESNO,
	   fn: function(buttonId ){
			if(buttonId=='yes'){
			
				var progressBar = new ProgressBar('Delete Variation Type', 'Processing variation type, please wait...');
				progressBar.showProgress();
				
				Ext.Ajax.request({
				   url: connector_url + 'variation_type_save',
				   params:{
						action:'delete',
						vtype_id:vtype_id
				   },
				   success: function(response, options){
						progressBar.hideProgress();
						var results = Ext.decode(response.responseText);
						
						if(results.success){			
						}
						else{
							Ext.Msg.show({
							   title:'Error',
							   msg: results.msg,
							   buttons: Ext.Msg.OK,
							   icon: Ext.MessageBox.ERROR
							});						
						}
						
						Ext.getCmp('pnlVariationTypesGrid').getStore().reload();
				   },
				   failure: function(response, options){
						progressBar.hideProgress();
						var results = Ext.decode(response.responseText);
						
						if(results.success){
							Ext.Msg.show({
							   title:'Success',
							   msg: results.msg,
							   buttons: Ext.Msg.OK,
							   icon: Ext.MessageBox.INFO
							});						
						}
						else{
							Ext.Msg.show({
							   title:'Error',
							   msg: results.msg,
							   buttons: Ext.Msg.OK,
							   icon: Ext.MessageBox.ERROR
							});						
						}
						
						Ext.getCmp('pnlVariationTypesGrid').getStore().reload();
				   }
				});					
			}
	   },
	   icon: Ext.MessageBox.WARNING
	});											
	return false;
	
}

function createUpdateVariationType(record){

	var isUpdate = false;
	if(record!=null){
		isUpdate = true;
	}

	var createVariationTypWin = new Ext.Window({
		title:(isUpdate?'Update Variation Type':'Create Variation Type'),
		modal:true,
		height:300,
		width:600,
		layout:'fit',
		items:[
			{
				xtype:'form',
				layout:'form',
				itemId:'frmVariationType',
				border:false,
				labelSeparator:'',
				bodyStyle:'padding:30px 30px 30px 30px;',
				items:[
					{
						xtype:'hidden',
						name:'seq'
					},				
					{
						xtype:'hidden',
						name:'vtype_id'
					},
					{
						xtype:'hidden',
						name: 'action',
						value:'create'
					},				
					{
						xtype:'textfield',
						fieldLabel:'Name',
						name:'name',
						width:'70%',
						allowBlank:false
					},
					{
						xtype:'textarea',
						fieldLabel:'Description',
						name:'description',
						width:'100%'
					}											
				]
			}
		],
		buttons:[
			{
				text:(isUpdate?'Update':'Save'),
				handler:function(btn){
					var frmVariationType = createVariationTypWin.getComponent('frmVariationType');
								
					if(!frmVariationType.getForm().isValid()){
						Ext.Msg.show({
						   title:'Error',
						   msg: 'Please fill all required fields.',
						   buttons: Ext.Msg.OK,
						   fn: function(){
						   },
						   icon: Ext.MessageBox.ERROR
						});											
						return false;
					}					
					
					var progressBar = new ProgressBar( (isUpdate?'Update Variation Type':'Create Variation Type'), 'Processing variation type, please wait...');					
					progressBar.showProgress();
					
					frmVariationType.getForm().submit({
						url: connector_url + 'variation_type_save',
						success: function(form, action) {
						
							progressBar.hideProgress();
							if(action.result.success){
								createVariationTypWin.destroy();
							}
							else{
								Ext.Msg.show({
								   title:'Error',
								   msg: action.result.msg,
								   buttons: Ext.Msg.OK,
								   fn: function(){
									createVariationTypWin.destroy();
								   },
								   icon: Ext.MessageBox.ERROR
								});													
							}
							
							Ext.getCmp('pnlVariationTypesGrid').getStore().reload();
																				
						},
						failure: function(form, action) {
							
							progressBar.hideProgress();
							var msg = '';
							
							switch (action.failureType) {
								case Ext.form.Action.CLIENT_INVALID:
									msg = 'Form fields may not be submitted with invalid values';
									break;
								case Ext.form.Action.CONNECT_FAILURE:
									msg = 'Ajax communication failed';
									break;
								case Ext.form.Action.SERVER_INVALID:
								   msg = action.result.msg;
						   }												
						
							Ext.Msg.show({
							   title:'Error',
							   msg: msg,
							   buttons: Ext.Msg.OK,
							   fn: function(){
								createVariationTypWin.destroy();
							   },
							   icon: Ext.MessageBox.ERROR
							});
							
						}
					});
					
					
				}
			},
			{
				text:'Cancel',
				handler:function(){
					 createVariationTypWin.destroy();
				}										
			}									
		]
	});

	createVariationTypWin.show();

	if(isUpdate){
		createVariationTypWin.getComponent('frmVariationType').getForm().loadRecord(record);
		createVariationTypWin.getComponent('frmVariationType').getForm().findField('action').setValue('update');
	}
	else{
		createVariationTypWin.getComponent('frmVariationType').getForm().findField('seq').setValue(Ext.getCmp('pnlVariationTypesGrid').getStore().getCount());
	}	
	
	createVariationTypWin.getComponent('frmVariationType').getForm().isValid();
}