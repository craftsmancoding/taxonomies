var Taxonomies = function(config) {
    config = config || {};
    Taxonomies.superclass.constructor.call(this,config);
};
Ext.extend(Taxonomies,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},tabs:{},combo:{},
    config: {
        connector_url: ''
    },
    inVersion: false
});
Ext.reg('versionx',Taxonomies);
Taxonomies = new Taxonomies();

Taxonomies.panel.TaxHeader = function(config) {
    config = config || {};
    config.title = config.title || 'New Section';
    Ext.apply(config,{
        border: false,
        forceLayout: true,
        width: '98%',
        items: [{
            html: '<h3 style="border-bottom: 1px solid; padding-top: 1em;">'+config.title+'</h3>'
        }]
    });
    Taxonomies.panel.TaxHeader.superclass.constructor.call(this,config);
};
Ext.extend(Taxonomies.panel.TaxHeader,MODx.Panel);
Ext.reg('taxonomies-panel-header',Taxonomies.panel.TaxHeader);
