MODx.creat.product.panel = function(config) {
    config = config || {record:{}};
    config.record = config.record || {};
    Ext.applyIf(config,{
        ,baseParams: {}
        ,id: 'modx-creating_product_panel'
        ,class_key: 'modDocument'
        ,resource: ''
        ,bodyStyle: ''
		,cls: 'container form-with-labels'
        ,defaults: { collapsible: false ,autoHeight: true }
        ,forceLayout: true
        ,items: this.getFields(config)
        ,fileUpload: true
        ,useLoadingMask: true
    });
    MODx.creat.product.panel.superclass.constructor.call(this,config);
};
Ext.extend(MODx.creat.product.panel,MODx.FormPanel,{
    initialized: false
    ,defaultClassKey: 'modDocument'
    ,classLexiconKey: 'document'
    ,rteElements: 'ta'
    ,rteLoaded: false
    ,setup: function() {
        if (!this.initialized) {
            this.getForm().setValues(this.config.record);
            var pcmb = this.getForm().findField('parent-cmb');
            if (pcmb && Ext.isEmpty(this.config.record.parent_pagetitle)) {
                pcmb.setValue('');
            } else if (pcmb) {
                pcmb.setValue(this.config.record.parent_pagetitle+' ('+this.config.record.parent+')');
            }
            if (!Ext.isEmpty(this.config.record.pagetitle)) {
                Ext.getCmp('modx-resource-header').getEl().update('<h2>'+Ext.util.Format.stripTags(this.config.record.pagetitle)+'</h2>');
            }
            if (!Ext.isEmpty(this.config.record.resourceGroups)) {
                var g = Ext.getCmp('modx-grid-resource-security');
                if (g && Ext.isEmpty(g.config.url)) {
                    var s = g.getStore();
                    if (s) { s.loadData(this.config.record.resourceGroups); }
                }
            }

            this.defaultClassKey = this.config.record.class_key || this.defaultClassKey;
            this.defaultValues = this.config.record || {};
            if ((this.config.record && this.config.record.richtext) || MODx.request.reload || MODx.request.activeSave == 1) {
                this.markDirty();
            }
        }
        if (MODx.config.use_editor && MODx.loadRTE) {
            var f = this.getForm().findField('richtext');
            if (f && f.getValue() == 1 && !this.rteLoaded) {
                MODx.loadRTE(this.rteElements);
                this.rteLoaded = true;
            } else if (f && f.getValue() == 0 && this.rteLoaded) {
                if (MODx.unloadRTE) {
                    MODx.unloadRTE(this.rteElements);
                }
                this.rteLoaded = false;
            }
        }

        this.fireEvent('ready');
        this.initialized = true;

        MODx.fireEvent('ready');
        MODx.sleep(4); /* delay load event to allow FC rules to move before loading RTE */
        if (MODx.afterTVLoad) { MODx.afterTVLoad(); }
        this.fireEvent('load');

    }

    ,getFields: function(config) {
        var it = [];
		it.push({
            title: 'Products'
            ,id: 'modx-Products'
            ,cls: 'modx-resource-tab'
            ,layout: 'fit'
            ,labelAlign: 'top'
            ,labelSeparator: ''
            ,bodyCssClass: 'tab-panel-wrapper main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
                ,width: 400
				, height:700
            }
            ,items:[]// this.getProductsFields(config)
        });
		it.push({
            title: 'Settings'
            ,id: 'modx-Settings'
            ,cls: 'modx-resource-tab'
            ,layout: 'form'
            ,labelAlign: 'top'
            ,labelSeparator: ''
            ,bodyCssClass: 'tab-panel-wrapper main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
                ,width: 400
				, height:550
            }
            ,items:[]// this.getStoreSettingsFields(config)
        });
        it.push({
            title:'Variations'
            ,id: 'modx-Variations'
            ,cls: 'modx-resource-tab'
            ,layout: 'form'
            ,labelAlign: 'top'
            ,labelSeparator: ''
            ,bodyCssClass: 'tab-panel-wrapper main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
                ,width: 400
            }
            ,items:[]// this.getMainFields(config)
        });
        it.push({
            id: 'modx-Specs'
            ,title:'Specs'
            ,cls: 'modx-resource-tab'
            ,layout: 'form'
            ,forceLayout: true
            ,deferredRender: false
            ,labelWidth: 200
            ,bodyCssClass: 'main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
            }
            ,items:[]// this.getSettingFields(config)
        });
    it.push({
            id: 'modx-Images'
            ,title:'Images'
            ,cls: 'modx-resource-tab'
            ,layout: 'form'
            ,forceLayout: true
            ,deferredRender: false
            ,labelWidth: 200
            ,bodyCssClass: 'main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
            }
            ,items: []//this.getSettingFields(config)
        });  
	it.push({
            id: 'modx-Taxonomies'
            ,title:'Taxonomies'
            ,cls: 'modx-resource-tab'
            ,layout: 'form'
            ,forceLayout: true
            ,deferredRender: false
            ,labelWidth: 200
            ,bodyCssClass: 'main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
            }
            ,items: []//this.getSettingFields(config)
        });
        var its = [];
        its.push(this.getPageHeader(config),{
            id:'modx-Products-tabs'
            ,xtype: 'modx-tabs'
            ,forceLayout: true
            ,deferredRender: false
            ,collapsible: true
            ,animCollapse: false
            ,itemId: 'tabs'
            ,items: it
        });
        return its;
    }
	,getPageHeader: function(config) {
        config = config || {record:{}};
        return {
            html: '<h2>'+_('document_new')+'</h2>'
            ,id: 'modx-product-header'
            ,cls: 'modx-page-header'
            ,border: false
            ,forceLayout: true
            ,anchor: '100%'
        };
    }

});
Ext.reg('modx-creating_product_panel',MODx.creat.product.panel);
var triggerDirtyField = function(fld) {
    Ext.getCmp('modx-creating_product_panel').fieldChangeEvent(fld);
};
MODx.fireResourceFormChange = function(f,nv,ov) {
    Ext.getCmp('modx-creating_product_panel').fireEvent('fieldChange');
};