<?php
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/create.class.php';

class TermCreateManagerController extends ResourceCreateManagerController {

    public $resource;


    public function loadCustomCssJs() {
        parent::loadCustomCssJs();
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('taxonomies.assets_url', null, MODX_ASSETS_URL.'components/taxonomies/');
        $this->addCss($assetsUrl.'css/mgr.css');
    }    
    
        
    public function getLanguageTopics() {
        return array('resource','taxonomies:default');
    }
    /**
     * Return the pagetitle
     *
     * @return string
     */
    public function getPageTitle() {
        return $this->modx->lexicon('term_new');
    }

}