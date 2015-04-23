<?php
/**
 * Class responsible for ajax functionality
 * Like returning json data and use it on js
 * Or loading a modal window
 */
namespace Taxonomies;
class AjaxController extends BaseController {

    function __construct(\modX &$modx,$config = array()) {
        parent::__construct($modx,$config);
        static::$x =& $modx;
        $this->tax = new Base($this->modx);
    }

    /**
     * getTerms
     * for ajax access see get_terms on taxonomies.js
     * @param array $scriptProperties
     * @return json
     */
    public function getTerms(array $scriptProperties = array()) {
        $this->loadHeader = false;
        $this->loadFooter = false;
        // GFD... this can't be set at runtime. See improvised addStandardLayout() function
        $this->loadBaseJavascript = false;
        $id = $this->modx->getOption('page_id',$scriptProperties);
        $terms =  $this->tax->getTerms($id);
        return json_encode($terms);
    }

    public function getTermsModal(array $scriptProperties = array())
    {
        $this->loadHeader = false;
        $this->loadFooter = false;
        // GFD... this can't be set at runtime. See improvised addStandardLayout() function
        $this->loadBaseJavascript = false;

        $this->setPlaceholder('loader', $this->config['assets_url'] . 'images/ajax-loader.GIF');
        $this->setPlaceholder('connector_url', $this->tax->getControllerUrl());
        $this->setPlaceholder('taxonomies',$this->tax->getTaxonomies());
        $this->setPlaceholder('taxonomy_id',$scriptProperties['page_id']);
        $this->setPlaceholder('terms',$this->tax->getTerms($scriptProperties['page_id']));

        return $this->fetchTemplate('modal/terms.php');
    }

    /**
     * Get an HTML list of terms with links to edit them.
     * URL format: /manager/?a=resource/update&id=____
     * TODO: a view file please!  Some formatting that doesn't suck!
     * @param $page_id
     * @return string
     */
    public function getTermList($page_id)
    {
        $this->modx->lexicon->load('taxonomies:default');
        $c = $this->modx->newQuery('PageTerm');
        $c->where(array('PageTerm.term_id' => $page_id)); // yes, term_id is the current page
        $c->sortby('Page.pagetitle','ASC');
        $Terms = $this->modx->getCollectionGraph('PageTerm','{"Page":{}}',$c);

        $this->setPlaceholder('terms',$Terms);
        return $this->fetchTemplate('main/page_tab.php');

    }
} 