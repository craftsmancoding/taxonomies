ProductImages = Ext.extend(Ext.Panel,{
	imageStore:new Ext.data.Store({
		autoLoad:true,
		url: connector_url + 'json_images&t=data',
		sortInfo:{
			field:'seq',
			direction: 'ASC'
		},
		reader:new Ext.data.JsonReader({
			idProperty: 'image_id',
			root: 'results',
			totalProperty: 'total',
			fields:[
				{name: 'image_id'},
				{name: 'product_id'},
				{name: 'title'},
				{name: 'alt'},
				{name: 'url'},
				{name: 'width'},
				{name: 'height'},
				{name: 'is_active'},
				{name: 'seq'}
			]
		})	
	}),
	constructor:function(config){
		var me = this;

		config = config || {};
		
		Ext.apply(config, {
			border:true,
			padding:5,
			height:400,
			layout:{
				type:'border'
			},
			items:[
				{
					region:'north',
					height:40,					
					border:false,
					xtype:'panel',
					layout:{
						type:'hbox',
						pack:'end',
						align:'middle'
					},					
					items:[
						{
							xtype:'button',
							text:'Upload',
							handler:function(){
							}
						},
						{
							xtype:'displayfield',
							value:' ',
							width:'5px'
						}			
					]
				},
				{
					region:'center',
					border:false,
					xtype:'dataview',
					id: 'images-view',
					store:this.imageStore,
					autoScroll: true,
					autoHeight: false,
					layout:'auto',
					overClass: 'x-view-over',
					itemSelector: 'div.thumb-wrap',
					enableDragDrop:true,
					ddGroup:'manageImagesDDGroup',					
					emptyText: 'No images to display',
					tpl:new Ext.XTemplate(
						'<tpl for=".">',
							'<div class="thumb-wrap" id="{image_id}" height="100px" width="100px">',
							'<div class="thumb"  height="100px" width="100px"><img src="{url}" title="{title}" height="100px" width="100px"></div>',
							'<span class="x-editable">{title}</span></div>',
						'</tpl>',
						'<div class="x-clear"></div>'
					),
					listeners:{
						render:function(pnl){

							var dragZone = new Ext.dd.DragZone(pnl.ownerCt.el, {
								ddGroup : 'manageImagesDDGroup',
								copy:false,
								getDragData : function(e){
									var sourceEl = e.getTarget(pnl.itemSelector, 10);
									dragZone.sourceEl = sourceEl;
									return {
										ddel:sourceEl
									};
								}	
							});

							var dropZone = new Ext.dd.DropZone(pnl.ownerCt.el, {
								ddGroup : 'manageImagesDDGroup',
								copy:false,
								notifyDrop : function(dd, e, data){
									var targetEl = e.getTarget(pnl.itemSelector, 10);
									
									if(targetEl!=null 
										&& dragZone!=null
										&& dragZone.sourceEl!=null
										&& targetEl.id!=dragZone.sourceEl.id){
										
										console.log('Source :: ' + dragZone.sourceEl.id);
										console.log('Traget :: ' + targetEl.id);
										
										var allElements = Ext.DomQuery.jsSelect(pnl.itemSelector,'images-view');
										
										if(allElements!=null){
										
											var arrIds = new Array();
										
											for(Icount=0;Icount<allElements.length;Icount++){
												if(allElements[Icount]!=null){
													arrIds.push(parseInt(allElements[Icount].id));
												}
											}
											
											console.log("Before :: " + Ext.encode(arrIds));
											
											var index = -1;
											for(Icount=0;Icount<arrIds.length;Icount++){
												if(arrIds[Icount]== parseInt(targetEl.id)){
													index = Icount;
													arrIds[Icount] = parseInt(dragZone.sourceEl.id);
												}
											}
											
											for(Icount=0;Icount<arrIds.length;Icount++){
												if(arrIds[Icount]== parseInt(dragZone.sourceEl.id) && index!=Icount){
													arrIds[Icount] = parseInt(targetEl.id);
												}
											}

											console.log("After :: " + Ext.encode(arrIds));
											
											pnl.store.each(function(record){
												for(Icount=0;Icount<arrIds.length;Icount++){
													if(record.get('image_id')==arrIds[Icount]){
														record.beginEdit();
														record.set('seq',Icount);
														record.endEdit();
														console.log("seq :: " + arrIds[Icount] + ' - ' + Icount);
													}
												}
											});
											
											pnl.store.sort('seq', 'ASC');
											pnl.refresh();
											
											Ext.Ajax.request({
											   url: connector_url + 'image_seq_save',
											   params:{
												image_ids:Ext.encode(arrIds)
											   },
											   success: function(response, options){					
											   },
											   failure: function(response, options){					
											   }
											});
												
											return true;
										}
									}
									return false;
								}
							});							
						
						}
					}
				}
			],
			listeners:{
				afterrender:function(){
					this.imageStore.load();
				}
			}
		},{});
	
		ProductImages.superclass.constructor.call(this, config);
	}
});


function showImageWindow(){
	var win = new Ext.Window({
		title:'Images',
		width:500,
		height:500,
		items:[
			new ProductImages()
		]
	});
	
	win.show();
}