function renderVariationTerms(vtype_id){

	var variationTermsStore = new Ext.data.Store({
		autoLoad:true,
		url: connector_url + 'json_variation_terms&t=data&vtype_id=' + vtype_id,
		sortInfo:{
			field:'seq',
			direction: 'ASC'
		},		
		reader:new Ext.data.JsonReader({
			idProperty: 'vterm_id',
			root: 'results',
			totalProperty: 'total',
			fields:[
				{name: 'vtype_id'},
				{name: 'vterm_id'},
				{name: 'variation_type'},
				{name: 'sku_suffix'},
				{name: 'sku_prefix'},
				{name: 'seq'},
				{name: 'name'}
			]
		})
	});

	var variationTermsContainer = new Ext.Panel({
		title:'Manage Variation Terms',
		renderTo:'moxycart_canvas',
		layout:'border',
		height:500,		
		items:[
			{
				region:'center',
				xtype:'grid',
				store:variationTermsStore,
				id:'pnlVariationTermsGrid',
				layout:'fit',
				autoExpandColumn: 'name',
				selModel:new Ext.grid.RowSelectionModel({
					singleSelect:true
				}),				
				loadMask:true,
				enableDragDrop:true,
				ddGroup:'variationTermsDDGroup',
				enableHdMenu:false,				
				viewConfig: {
					forceFit: true,
					getRowClass:function(record, rowIndex, rp, ds){
						return 'moxycart-grid-row';
					},
					emptyText : 'You don\'t have any variation terms yet.'					
				},				
				cm:new Ext.grid.ColumnModel([
					  {
						header:'Name',
						dataIndex: 'name'
					  },
					  {
						header: 'SKU Prefix',
						dataIndex: 'sku_prefix'
					  },
					  {
						header: 'SKU Suffix',
						dataIndex: 'sku_suffix'
					  },
					  {
						header:'',
						dataIndex: 'note',
						width:200,
						fixed:true,
						align:'center',
						renderer:function(value, metaData, record, rowIndex, colIndex, store){
							var html =  '<input type="button" value="Edit" class="x-btn x-btn-noicon x-box-item" style="height:20px;width:60px;"   onclick="return onVariationTermEdit(' + record.get('vterm_id') + ');"/>';
							
							html += '&nbsp;&nbsp;';
							
							html += '<input type="button" value="Delete" class="x-btn x-btn-noicon x-box-item" style="height:20px;width:60px;"   onclick="return onVariationTermDelete(' + record.get('vterm_id') + ');"/>';
							
							return html;
						}
					  }
				]),
				listeners:{
					render:function(){
					
						var grid = Ext.getCmp('pnlVariationTermsGrid');
					
						var ddrow = new Ext.dd.DropTarget(grid.container, {
							ddGroup : 'variationTermsDDGroup',
							copy:false,
							notifyDrop : function(dd, e, data){

								var grid = Ext.getCmp('pnlVariationTermsGrid');
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
												   url: connector_url + 'variation_term_save',
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
						text:'Create Variation Term',
						handler:function(){
							createUpdateVariationTerm(null, vtype_id);
						}
					},
					{
						xtype:'button',
						text:'Close',
						width:100,
						handler:function(){
							MODx.loadPage(MODx.action['moxycart:index'], 'f=variation_types_manage');
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

function onVariationTermDelete(vterm_id){

	Ext.Msg.show({
	   title:'Confirm',
	   msg: 'Are you sure want to delete variation term?',
	   buttons: Ext.Msg.YESNO,
	   fn: function(buttonId ){
			if(buttonId=='yes'){
			
				var progressBar = new ProgressBar('Delete Variation Term', 'Processing variation term, please wait...');
				progressBar.showProgress();
				
				Ext.Ajax.request({
				   url: connector_url + 'variation_term_save',
				   params:{
						action:'delete',
						vterm_id:vterm_id
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
						
						Ext.getCmp('pnlVariationTermsGrid').getStore().reload();
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
						
						Ext.getCmp('pnlVariationTermsGrid').getStore().reload();
				   }
				});					
			}
	   },
	   icon: Ext.MessageBox.WARNING
	});											
	return false;

}

function onVariationTermEdit(vterm_id){

	var pnlVariationTermsGrid = Ext.getCmp('pnlVariationTermsGrid');
	var selModel = pnlVariationTermsGrid.getSelectionModel();
	
	if(selModel!=null && selModel.hasSelection()){
		var record  = selModel.getSelected();
		createUpdateVariationTerm(record, record.get('vtype_id'));
	}
	else{
		Ext.Msg.show({
		   title:'Error',
		   msg: 'Please select a row to edit variation term.',
		   buttons: Ext.Msg.OK,
		   icon: Ext.MessageBox.ERROR
		});		
	}

}

function createUpdateVariationTerm(record, vtype_id){

	var isUpdate = false;
	if(record!=null){
		isUpdate = true;
	}

	var createVariationTermWin = new Ext.Window({
		title:(isUpdate?'Update Variation Term':'Create Variation Term'),
		modal:true,
		height:300,
		width:600,
		layout:'fit',
		items:[
			{
				xtype:'form',
				layout:'form',
				itemId:'frmVariationTerm',
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
						name:'vterm_id'
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
						xtype:'textfield',
						fieldLabel:'SKU Prefix',
						name:'sku_prefix',
						width:'100%'
					},
					{
						xtype:'textfield',
						fieldLabel:'SKU Suffix',
						name:'sku_suffix',
						width:'100%'
					}						
				]
			}
		],
		buttons:[
			{
				text:(isUpdate?'Update':'Save'),
				handler:function(btn){
					var frmVariationTerm = createVariationTermWin.getComponent('frmVariationTerm');
								
					if(!frmVariationTerm.getForm().isValid()){
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
					
					var progressBar = new ProgressBar( (isUpdate?'Update Variation Term':'Create Variation Term'), 'Processing variation term, please wait...');					
					progressBar.showProgress();
					
					frmVariationTerm.getForm().submit({
						url: connector_url + 'variation_term_save',
						success: function(form, action) {
						
							progressBar.hideProgress();
							if(action.result.success){
								createVariationTermWin.destroy();
							}
							else{
								Ext.Msg.show({
								   title:'Error',
								   msg: action.result.msg,
								   buttons: Ext.Msg.OK,
								   fn: function(){
									createVariationTermWin.destroy();
								   },
								   icon: Ext.MessageBox.ERROR
								});													
							}
							
							Ext.getCmp('pnlVariationTermsGrid').getStore().reload();
																				
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
								createVariationTermWin.destroy();
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
					 createVariationTermWin.destroy();
				}										
			}									
		]
	});

	createVariationTermWin.show();

	if(isUpdate){
		createVariationTermWin.getComponent('frmVariationTerm').getForm().loadRecord(record);
		createVariationTermWin.getComponent('frmVariationTerm').getForm().findField('action').setValue('update');
	}
	else{
		createVariationTermWin.getComponent('frmVariationTerm').getForm().findField('vtype_id').setValue(vtype_id);
		createVariationTermWin.getComponent('frmVariationTerm').getForm().findField('seq').setValue(Ext.getCmp('pnlVariationTermsGrid').getStore().getCount());
	}
	
	createVariationTermWin.getComponent('frmVariationTerm').getForm().isValid();

}