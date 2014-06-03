<?php 
/**
 * Base class for stuff
 */
namespace Taxonomies;
class Base {

    public $modx;
    public $old_log_level;
    
    public function __construct(&$modx) {
        $this->modx =& $modx;       
    }

    public function __destruct() {
        $this->modx->setLogLevel($this->old_log_level);
        /*
        // TODO
        $xpdo->setLogTarget(array(
           'target' => 'FILE',
           'options' => array(
               'filename' => 'install.' . strftime('%Y-%m-%dT%H:%M:%S')
            )
        ));
        */
    }
    
    /**
     * Logging Snippet info
     *
     */
    public function log($snippetName, $scriptProperties) {
        $log_level = $this->modx->getOption('log_level',$scriptProperties, $this->modx->getOption('log_level'));
        $this->old_log_level = $this->modx->setLogLevel($log_level);
        
        // TODO
        //$this->old_log_target = $this->modx->getOption('log_level');
        //$log_target = $this->modx->getOption('log_target',$scriptProperties);
 
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, "scriptProperties:\n".print_r($scriptProperties,true),'','Snippet '.$snippetName);
    }


    /**
     * Format a record set:
     *
     * Frequently Snippets need to iterate over a record set. Each record should be formatted 
     * using the $innerTpl, and the final output should be optionally wrapped in the $outerTpl.
     *
     * See http://rtfm.modx.com/revolution/2.x/developing-in-modx/other-development-resources/class-reference/modx/modx.getchunk
     *
     * @param array of arrays (a simple record set), or an array of objects (an xPDO Collection)
     * @param string formatting $innerTpl formatting string OR chunk name
     * @param string formatting $outerTpl formatting string OR chunk name (optional)
     * @return string
     */
    public function format($records,$innerTpl,$outerTpl=null) {
        if (empty($records)) {
            return '';
        }
        
        // A Chunk Name was passed
        $use_tmp_chunk = false;
        if (!$innerChunk = $this->modx->getObject('modChunk', array('name' => $innerTpl))) {
            $use_tmp_chunk = true;
        }
        
        $out = '';
        foreach ($records as $r) {
            if (is_object($r)) $r = $r->toArray(); // Handle xPDO objects
            // Use a temporary Chunk when dealing with raw formatting strings
            if ($use_tmp_chunk) {
                $uniqid = uniqid();
                $innerChunk = $this->modx->newObject('modChunk', array('name' => "{tmp-inner}-{$uniqid}"));
                $innerChunk->setCacheable(false);    
                $out .= $innerChunk->process($r, $innerTpl);
            }
            // Use getChunk when a chunk name was passed
            else {
                $out .= $this->modx->getChunk($innerTpl, $r);
            }
        }
        
        if ($outerTpl) {
            $props = array('content'=>$out);
            // Formatting String
            if (!$outerChunk = $this->modx->getObject('modChunk', array('name' => $outerTpl))) {  
                $uniqid = uniqid();
                $outerChunk = $this->modx->newObject('modChunk', array('name' => "{tmp-outer}-{$uniqid}"));
                $outerChunk->setCacheable(false);    
                return $outerChunk->process($props, $outerTpl);        
            }
            // Chunk Name
            else {
                return $this->modx->getChunk($outerTpl, $props);
            }
        }
        return $out;
    }
    
    /**
     * getTnT : Taxonomies and Terms. BOOM.
     *
     * Get data structure describing taxonomy/terms for use in the form.
     * TODO: use the json caching here.
     * @return array containing structure compatible w Formbuilder
     */
    public function getTnT() {
        $data = array();
        $c = $this->modx->newQuery('Taxonomy');
        $c->where(array('published'=>true,'class_key'=>'Taxonomy'));
        $c->sortby('menuindex','ASC');        
        if ($Ts = $this->modx->getCollection('Taxonomy', $c)) {
            foreach ($Ts as $t) {
/*
                $props = $t->get('properties');
                if (!isset($props['children'])) {
                    continue;
                }
*/
                $c = $this->modx->newQuery('Term');
                $c->where(array('published'=>true,'class_key'=>'Term','parent'=>$t->get('id')));
                $c->sortby('menuindex','ASC');        
                if ($Terms = $this->modx->getCollection('Term', $c)) {
                    foreach ($Terms as $term) {
                        $data[ $t->get('pagetitle') ][ $term->get('id') ] = $term->get('pagetitle');
                    }
                }
                // Plug the spot for the taxonomy?
                else {
                    $data[ $t->get('pagetitle') ] = array();
                }
            }
        }
        $this->modx->log(1,'TnT: '.print_r($data,true));
        return $data;
    }
    
    /**
     * Get an array of term_ids for any associations with the given page_id
     * @param integer $page_id
     */
    public function getPageTerms($page_id) {
        $out = array();
        if ($Terms = $this->modx->getCollection('PageTerm', array('page_id'=>$page_id))) {
            foreach ($Terms as $t) {
                $out[] = $t->get('term_id');
            }
        }
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, "pageTerms for page ".$page_id.': '.print_r($out,true),'',__CLASS__);        
        return $out;
    }

    /**
     * getForm
     *
     * @param integer $page_id current MODX resource
     * @return string HTML form.
     */
    public function getForm($page_id) {
        $data = $this->getTnT();
        $current_values = $this->getPageTerms($page_id);
        
        $out = \Formbuilder\Form::multicheck('terms',$data,$current_values); 
        return $out;
    }


    /**
     * Dictate all page terms (array) for the given page_id (int)
     * @param integer $page_id
     * @param array $terms
     *
     */
    public function dictatePageTerms($page_id,$terms) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, "Dictating taxonomy term_ids for page ".$page_id.': '.print_r($terms,true),'',__CLASS__);
        $existing = $this->modx->getCollection('PageTerm',array('page_id'=>$page_id));
        
        $ex_array = array();
        foreach ($existing as $e) {
            if (!in_array($e->get('term_id', $terms))) {
                $e->remove();
            }
        }
        // Reorder
        $seq = 0;
        foreach ($terms as $term_id) {
            if (!$pt = $this->modx->getObject('PageTerm',array('page_id'=>$page_id,'term_id'=>$term_id))) {
                $pt = $this->modx->newObject('PageTerm',array('page_id'=>$page_id,'term_id'=>$term_id));
            }
            $pt->set('seq', $seq);
            $pt->save();
            $seq++;
        }
    }
}