function renderManageSpecs(){

	var specsStore = new Ext.data.Store({
		autoLoad:true,
		url: connector_url + 'json_specs&t=data',
		sortInfo:{
			field:'seq',
			direction: 'ASC'
		},
		reader:new Ext.data.JsonReader({
			idProperty: 'spec_id',
			root: 'results',
			totalProperty: 'total',
			fields:[
				{name: 'spec_id'},
				{name: 'identifier'},
				{name: 'name'},
				{name: 'description'},
				{name: 'group'},
				{name: 'type'},
				{name: 'seq'}
			]
		})
	});

	var specsContainer = new Ext.Panel({
		title:'Manage Specs',
		renderTo:'moxycart_canvas',
		layout:{
			type:'border'
		},
		height:500,		
		items:[
			{
				region:'center',
				xtype:'grid',
				id:'pnlSpecsGrid',
				store:specsStore,
				layout:'fit',
				autoExpandColumn: 'name',
				selModel:new Ext.grid.RowSelectionModel({
					singleSelect:true
				}),				
				loadMask:true,
				enableDragDrop:true,
				ddGroup:'specDDGroup',
				enableHdMenu:false,
				viewConfig: {
					autoFill: true,
					forceFit: true,
					getRowClass:function(record, rowIndex, rp, ds){
						return 'moxycart-grid-row';
					}
				},				
				cm:new Ext.grid.ColumnModel([
					  {
						header:'Name',
						resizable: false,
						dataIndex: 'name'
					  },
					  {
						header: 'Group',
						dataIndex: 'group'
					  },
					  {
						header: 'Description',
						dataIndex: 'description'
					  },
					  {
						header:'',
						dataIndex: 'spec_id',
						editable:false,
						width:200,
						fixed:true,
						align:'center',
						renderer:function(value, metaData, record, rowIndex, colIndex, store){
							var html =  '<input type="button" value="Edit" class="x-btn x-btn-noicon x-box-item" style="height:25px;width:90px;" onclick="return onSpecsEdit(' + record.get('spec_id') + ');"/>';
							
							html += '&nbsp;&nbsp;';
							
							html += '<input type="button" value="Delete" class="x-btn x-btn-noicon x-box-item" style="height:25px;width:90px;" onclick="return onSpecsDelete(' + record.get('spec_id') + ');"/>';
												
							return html;
						}
					  }
				]),
				listeners:{
					dblclick:function(){},
					render:function(){
					
						var grid = Ext.getCmp('pnlSpecsGrid');
					
						var ddrow = new Ext.dd.DropTarget(grid.container, {
							ddGroup : 'specDDGroup',
							copy:false,
							notifyDrop : function(dd, e, data){

								var grid = Ext.getCmp('pnlSpecsGrid');
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
												   url: connector_url + 'spec_save',
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
						text:'Create Spec',
						handler:function(){
							createUpdateSpec(null);
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

function createUpdateSpec(record){

	var isUpdate = false;
	if(record!=null){
		isUpdate=true;
	}

	var createSpecWin = new Ext.Window({
		title:(isUpdate?'Update Spec : ' + record.get('name'):'Create Spec'),
		modal:true,
		height:400,
		width:600,
		layout:'fit',
		items:[
			{
				xtype:'form',
				layout:'form',
				itemId:'frmSpecs',
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
						name:'spec_id'
					},
					{
						xtype:'hidden',
						name: 'action',
						value:'create'
					},
					{
						xtype:'textfield',
						fieldLabel:'Name',
						width:'70%',
						name: 'name',
						allowBlank:false
					},
					{
						xtype:'textfield',
						fieldLabel:'Identifier',
						width:'70%',
						name: 'identifier',
						allowBlank:false
					},
					{
						xtype:'textfield',
						fieldLabel:'Group',
						width:'70%',
						name: 'group'
					},
					{
						xtype:'textarea',
						fieldLabel:'Description',
						width:'100%',
						name: 'description'
					}											
				]
			}
		],
		buttons:[
			{
				text:(isUpdate?'Update':'Save'),
				handler:function(btn){
					var frmSpecs = createSpecWin.getComponent('frmSpecs');
					
					if(!frmSpecs.getForm().isValid()){
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
					
					var progressBar = new ProgressBar( (isUpdate?'Update Specs':'Create Specs'), 'Processing specs, please wait...');					
					progressBar.showProgress();
					
					frmSpecs.getForm().submit({
						url: connector_url + 'spec_save',
						success: function(form, action) {
							progressBar.hideProgress();
							if(action.result.success){
								createSpecWin.destroy();
							}
							else{
								Ext.Msg.show({
								   title:'Error',
								   msg: action.result.msg,
								   buttons: Ext.Msg.OK,
								   fn: function(){
									createSpecWin.destroy();
								   },
								   icon: Ext.MessageBox.ERROR
								});													
							}
							
							Ext.getCmp('pnlSpecsGrid').getStore().reload();
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
								createSpecWin.destroy();
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
					createSpecWin.destroy();
				}										
			}									
		]
	});

	createSpecWin.show();
	
	if(isUpdate){
		createSpecWin.getComponent('frmSpecs').getForm().loadRecord(record);
		createSpecWin.getComponent('frmSpecs').getForm().findField('action').setValue('update');
	}
	else{
		createSpecWin.getComponent('frmSpecs').getForm().findField('seq').setValue(Ext.getCmp('pnlSpecsGrid').getStore().getCount());
	}
	
	createSpecWin.getComponent('frmSpecs').getForm().isValid();
	

}

function onSpecsEdit(spec_id){
	onDblClickGrid();
}

function onSpecsDelete(spec_id){

	Ext.Msg.show({
	   title:'Confirm',
	   msg: 'Are you sure want to delete this spec?',
	   buttons: Ext.Msg.YESNO,
	   fn: function(buttonId ){
			if(buttonId=='yes'){
			
				var progressBar = new ProgressBar('Delete Specs', 'Processing specs, please wait...');
				progressBar.showProgress();
				
				Ext.Ajax.request({
				   url: connector_url + 'spec_save',
				   params:{
						action:'delete',
						spec_id:spec_id
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
						
						Ext.getCmp('pnlSpecsGrid').getStore().reload();
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
						
						Ext.getCmp('pnlSpecsGrid').getStore().reload();
				   }
				});					
			}
	   },
	   icon: Ext.MessageBox.WARNING
	});											
	return false;

}

function onDblClickGrid(){

	var pnlSpecsGrid = Ext.getCmp('pnlSpecsGrid');
	var selModel = pnlSpecsGrid.getSelectionModel();
	
	if(selModel!=null && selModel.hasSelection()){
		var record  = selModel.getSelected();
		createUpdateSpec(record);
	}
	else{
		Ext.Msg.show({
		   title:'Error',
		   msg: 'Please select a row to edit spec.',
		   buttons: Ext.Msg.OK,
		   icon: Ext.MessageBox.ERROR
		});		
	}

}