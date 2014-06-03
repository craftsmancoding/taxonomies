<?php
/**
 * @name Taxonomies
 * @description 
 * @PluginEvents OnManagerPageInit,OnDocFormPrerender,OnDocFormSave
 *
    <script type="text/javascript">
    MODx.on("ready",function() {
        MODx.addTab("modx-resource-tabs",{
            title: _('versionx.tabheader'),
            id: 'versionx-resource-tab',
            width: '95%',
            items: [{
                xtype: 'versionx-panel-resources',
                //layout: 'anchor',
                width: 500
            },{
                html: '<hr />',
                width: '95%'
            },{
                layout: 'anchor',
                anchor: '1',
                items: [{
                    xtype: 'versionx-grid-resources'
                }]
            }]
        });
    });
    </script>
 
 */
 
$core_path = $modx->getOption('taxonomies.core_path', null, MODX_CORE_PATH.'components/taxonomies/');
include_once $core_path .'vendor/autoload.php';
$cache_dir = 'taxonomies';

switch ($modx->event->name) {

    //------------------------------------------------------------------------------
    //! OnManagerPageInit
    //  Load up custom CSS for the manager
    //------------------------------------------------------------------------------
    case 'OnManagerPageInit':
        $assets_url = $modx->getOption('taxonomies.assets_url', null, MODX_ASSETS_URL.'components/taxonomies/');
        $modx->log(modX::LOG_LEVEL_DEBUG,'Registering '.$assets_url.'css/taxonomies.css','','taxonomies Plugin:OnManagerPageInit');
        $modx->regClientCSS($assets_url.'css/taxonomies.tree.css');
        break;
    //------------------------------------------------------------------------------
    //! OnDocFormPrerender
    // Add a custom tab to the resource panel. $resource will be null for new Resources!
    // $scriptProperties will be populated on an update.
    //------------------------------------------------------------------------------
    case 'OnDocFormPrerender':
        $modx->log(modX::LOG_LEVEL_DEBUG,'Getting Test','taxonomies Plugin:OnDocFormPrerender');
        $skip_classes = array('Taxonomy','Term');
        if ($mode == 'new') {
            $class_key = (isset($_GET['class_key'])) ? $_GET['class_key'] : 'modDocument';            
        } 
        else {
            $class_key = $resource->get('class_key');    
        }

        if (!in_array($class_key,$skip_classes)) {
            $T = new \Taxonomies\Base($modx);
            $form = $T->getForm($id);
            $modx->regClientStartupHTMLBlock('<script type="text/javascript">
                MODx.on("ready",function() {
                    MODx.addTab("modx-resource-tabs",{
                        title: "Taxonomies",
                        id: "taxonomies-resource-tab",
                        width: "95%",
                        html: '.json_encode(utf8_encode("$form")).'
                    });
                });                
            </script>');
        }
        break;
        
    case 'OnDocFormSave':
        $modx->log(modX::LOG_LEVEL_DEBUG,'','','taxonomies Plugin:OnDocFormSave');
        $terms = $resource->get('terms');
        $T = new \Taxonomies\Base($modx);
        $T->dictatePageTerms($resource->get('id'), $terms);
        break;
}