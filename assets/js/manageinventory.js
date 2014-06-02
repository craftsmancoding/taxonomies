var productSortStore;

function renderManageInventoryPanel(){
	
	var pageSize = 10;
	
	productSortStore = new Ext.data.Store({
		autoLoad:false,
		// url: connector_url + 'json_products',
		url: store_url,
		sortInfo:{
			field:'seq',
			direction: 'ASC'
		},
		reader:new Ext.data.JsonReader({
			idProperty: 'product_id',
			root: 'results',
			totalProperty: 'total',
			fields:[
				{name: 'product_id'},
				{name: 'name'},
				{name: 'sku'},
				{name: 'type'},
				{name: 'qty_inventory'},
				{name: 'qty_alert'},
				{name: 'price'},
				{name: 'category'},
				{name: 'uri'},
				{name: 'is_active'},
				{name: 'variant'},				
				{name: 'seq'}
			]
		}),
		listeners:{
			load:function(store, records, options ){

				var pnlManageInventory = Ext.getCmp('pnlManageInventory');
				
				if(pnlManageInventory!=null){
					pnlManageInventory.removeAll();
					
					var h = '<form name="frmManageInventory" id="frmManageInventory">';
					
					h += '<table width="100%" height="100%" cellpadding="0" cellspacing="0">';	
					
					h += '<tr class="x-grid3-header x-grid3-hd-row" height="30px">';
					
					//Product
					h += '<td style="padding-left:5px;">';
					h += 'Product';
					h += '</td>';
					
					//SKU
					h += '<td>';
					h += 'SKU';
					h += '</td>';	

					//Variant
					h += '<td>';
					h += 'Variant';
					h += '</td>';

					//Inventory
					h += '<td width="80px">';
					h += 'Inventory';
					h += '</td>';	

					//Alert
					h += '<td width="80px">';
					h += 'Alert';
					h += '</td>';							
					
					//Change
					h += '<td width="80px">';
					h += 'Change	';
					h += '</td>';
					
					h += '</tr>';
				
					if(records!=null){
						for(Icount=0;Icount<records.length;Icount++){
												
							h += '<tr height="35px">';
							
							//Product
							h += '<td style="padding-left:5px;">';
							h += '' + records[Icount].get('name');
							h += '</td>';
							
							//SKU
							h += '<td>';
							h += '' + records[Icount].get('sku');
							h += '</td>';	

							//Variant
							h += '<td>';
							h += '' + records[Icount].get('variant');
							h += '</td>';

							//Inventory
							h += '<td align="right">';
							
							if(records[Icount].get('qty_inventory')<=records[Icount].get('qty_alert')){
								h += '<label id="qtyInventory' + records[Icount].get('product_id') + '" class="moxycart-bold-red" style="padding-right:10px; color:red;">' + records[Icount].get('qty_inventory') + '<label/>';
							}
							else{
								h += '<label id="qtyInventory' + records[Icount].get('product_id') + '" style="padding-right:10px;">' + records[Icount].get('qty_inventory') + '<label/>';
							}														
							h += '</td>';	

							//Alert
							h += '<td align="left">';
							h += ' <input type="hidden" name="products['+records[Icount].get('product_id')+'][product_id]" value="'+records[Icount].get('product_id')+'"/>';
							h += ' <input type="text" id="qtyAlert' + records[Icount].get('product_id') + '" name="products['+records[Icount].get('product_id')+'][qty_alert]" onkeypress="return validateQty(this);" onkeyup="return validateAlertQty(this,' + records[Icount].get('product_id') + ');" style="text-align:right;width:30px;" value="' + records[Icount].get('qty_alert') + '" onfocus="this.className=\'x-form-focus\';" onblur="this.className=\'\';"/>';
							h += '</td>';							
							
							//Change
							h += '<td align="left">';
							h += ' <input type="text" id="qtyChange' + records[Icount].get('product_id') + '" name="products['+records[Icount].get('product_id')+'][qty_change]" onkeypress="return validateQty(this);" onkeyup="return validateAlertQty(this,' + records[Icount].get('product_id') + ');" style="text-align:right;width:55px;" value="0"  onfocus="this.className=\'x-form-focus\';" onblur="this.className=\'\';"/>';
													
							h += '</td>';
							
							h += '</tr>';
						
						}
					}
					
					h += '</table>';
					
					h += '</form>';
					
					var row = new Ext.Panel({
						border:false,
						width:'100%',
						html:h								
					});
					pnlManageInventory.add(row);					
					
					pnlManageInventory.doLayout(false,true);
				}
			}
		}
	});


	productSortStore.load({
		params: {
			start: 0,          
			limit: pageSize,
		}
	});
	
	var manageInventoryContainer = new Ext.Panel({
		title:'Manage Inventory',
		renderTo:'moxycart_canvas',
		layout:{
			type:'border'
		},
		height:590,		
		items:[
			{
				region:'center',
				xtype:'panel',
				id:'pnlManageInventory',
				layout:{
					type:'vbox',
					align:'stretch',
					pack:'start'
				},
				bbar: new Ext.PagingToolbar({
					store: productSortStore,
					displayInfo: true,
					pageSize: pageSize,
					prependButtons: true
				})				
			},
			{
				region:'north',
				height:30,
				xtype:'panel',
				border:false
			},
			{
				region:'south',
				height:90,
				xtype:'panel',
				bodyStyle:'padding:10px 0px 10px 30px;',
				border:false,
				layout:{
					type:'hbox',
					pack:'center',
					align:'middle'
				},				
				items:[
					{
						xtype:'button',
						text:'Update Inventory',
						handler:function(){						
							updateInventory();
						}
					},
					{
						xtype:'button',
						text:'Close',
						cls: 'custom-close-btn',
						handler:function(){						
							backToParent();
						}
					}
				]
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

function updateInventory(){
	var frm = new Ext.form.BasicForm(document.getElementById('frmManageInventory'));
	
	var progressBar = new ProgressBar( ('Update Inventory'), 'Processing specs, please wait...');					
	progressBar.showProgress();

	frm.submit({
		url: connector_url + 'product_inventory_save',
		success: function(form, action) {
			progressBar.hideProgress();
			
			if(action.result.success){

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
            productSortStore.reload();			
			//Ext.getCmp('pnlCurrenciesGrid').getStore().reload();
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

			   },
			   icon: Ext.MessageBox.ERROR
			});
			
		}
	});	
}

function validateQty(txtField){
	/*if(event.keyCode>=48 && event.keyCode<=57){
		return true;
	}*/
	return true;
}

function validateAlertQty(txtField, product_id){
	
	var qtyAlert = document.getElementById('qtyAlert' + product_id);
	var qtyInventory = document.getElementById('qtyInventory' + product_id);
	var qtyChange = document.getElementById('qtyChange' + product_id);
	
	if(parseInt(qtyInventory.innerHTML)<=parseInt(qtyAlert.value)
		|| ( (parseInt(qtyInventory.innerHTML) + parseInt(qtyChange.value)) <= (parseInt(qtyAlert.value)) ) ){
		qtyInventory.className = 'moxycart-bold-red';
	}
	else{
		qtyInventory.className = '';
	}
	
	return false;
}

/**
 * See Moxycart::product_inventory()
 */
function backToParent() {
    window.location = back_url;
}