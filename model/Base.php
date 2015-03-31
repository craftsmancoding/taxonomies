<?php
/**
 * Base class for stuff
 */
namespace Taxonomies;
class Base
{

    public $modx;
    public $old_log_level;
    public $i = 0;

    public function __construct(&$modx)
    {
        $this->modx =& $modx;
    }

    public function __destruct()
    {
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
    public function log($snippetName, $scriptProperties)
    {
        $log_level = $this->modx->getOption('log_level', $scriptProperties, $this->modx->getOption('log_level'));
        $this->old_log_level = $this->modx->setLogLevel($log_level);

        // TODO
        //$this->old_log_target = $this->modx->getOption('log_level');
        //$log_target = $this->modx->getOption('log_target',$scriptProperties);

        $this->modx->log(\modX::LOG_LEVEL_DEBUG, "scriptProperties:\n" . print_r($scriptProperties, true), '', 'Snippet ' . $snippetName);
    }


    /**
     * Format a record set:
     *
     * Frequently Snippets need to iterate over a record set. Each record should be formatted
     * using the $innerTpl, and the final output should be optionally wrapped in the $outerTpl.
     *
     * See http://rtfm.modx.com/revolution/2.x/developing-in-modx/other-development-resources/class-reference/modx/modx.getchunk
     *
     * @param array $records array of arrays (a simple record set), or an array of objects (an xPDO Collection)
     * @param string formatting $innerTpl formatting string OR chunk name
     * @param string formatting $outerTpl formatting string OR chunk name (optional)
     * @param string $separator used to "glue" records together (e.g. comma-separated list)
     * @return string
     */
    public function format($records, $innerTpl, $outerTpl = null,$separator='')
    {
        if (empty($records)) {
            return '';
        }

        // A Chunk Name was passed
        $use_real_chunk = false;
        if ($innerChunk = $this->modx->getObject('modChunk', array('name' => $innerTpl))) {
            $use_real_chunk = true;
        }

        $out = array();
        foreach ($records as $r) {
            if (is_object($r)) {
                $r = $this->flattenArray($r->toArray('', false, false, true)); // Handle xPDO objects
            }

            // Pull up a real chunk
            if ($use_real_chunk) {
                $out[] = $this->modx->getChunk($innerTpl, $r);
            } // Use a temporary Chunk when dealing with raw formatting strings
            else {
                $uniqid = uniqid();
                $innerChunk = $this->modx->newObject('modChunk', array('name' => "{tmp-inner}-{$uniqid}"));
                $innerChunk->setCacheable(false);
                $out[] = $innerChunk->process($r, $innerTpl);
            }
        }
        $out = implode($separator, $out);

        if ($outerTpl) {
            $props = array('content' => $out);
            // Formatting String
            if (!$outerChunk = $this->modx->getObject('modChunk', array('name' => $outerTpl))) {
                $uniqid = uniqid();
                $outerChunk = $this->modx->newObject('modChunk', array('name' => "{tmp-outer}-{$uniqid}"));
                $outerChunk->setCacheable(false);
                return $outerChunk->process($props, $outerTpl);
            } // Chunk Name
            else {
                return $this->modx->getChunk($outerTpl, $props);
            }
        }
        return $out;
    }

    /**
     * getTaxonomiesAndTerms : Taxonomies and Terms. BOOM.
     *
     * Get data structure describing taxonomy/terms for use in the form.
     * TODO: use the json caching here.
     * @return array containing structure compatible w Formbuilder: $data['Taxonomy Name']['Term Name'] = page ID
     */
    public function getTaxonomiesAndTerms()
    {
        $data = array();
        $c = $this->modx->newQuery('Taxonomy');
        $c->where(array('published' => true, 'class_key' => 'Taxonomy'));
        $c->sortby('menuindex', 'ASC');

        if ($Ts = $this->modx->getCollection('Taxonomy', $c)) {
            foreach ($Ts as $t) {
                $data[$t->get('pagetitle')] = array();
                $c = $this->modx->newQuery('Term');
                $c->where(array('published' => true, 'class_key' => 'Term', 'parent' => $t->get('id')));
                $c->sortby('menuindex', 'ASC');
                if ($Terms = $this->modx->getCollection('Term', $c)) {
                    foreach ($Terms as $term) {
                        $data[$t->get('pagetitle')][$term->get('id')] = $term->get('pagetitle');
                    }
                } // Plug the spot for the taxonomy?
                else {
                    $data[$t->get('pagetitle')] = array();
                }
            }
        }

        return $data;
    }

    /**
     *  get all taxonomies
     *
     * @return mixed
     */
    public function getTaxonomies()
    {
        $c = $this->modx->newQuery('Taxonomy');
        $c->select('id,pagetitle,alias');
        $c->where(array('published' => true, 'class_key' => 'Taxonomy'));
        $c->sortby('menuindex', 'ASC');
        return $this->modx->getCollection('Taxonomy', $c);
    }

    /**
     *  get all immediate child terms
     * @param int $parent
     * @return mixed
     */
    public function getTerms($parent)
    {
        $terms = array();
        $c = $this->modx->newQuery('Term');
        $c->where(array('published' => true, 'class_key' => 'Term', 'parent' => $parent));
        $c->sortby('menuindex', 'ASC');
        if ($Ts = $this->modx->getCollection('Term', $c))
        {
            foreach ($Ts as $t) {
                $terms[]= array(
                    'id'=>$t->get('id'),
                    'pagetitle' => $t->get('pagetitle')
                );
            }
        }
        return $terms;
    }

    /**
     * @param array $current_values
     * @return string
     */
    public function buildForm($current_values)
    {
        $out = '';
        $c = $this->modx->newQuery('Taxonomy');
        $c->where(array('published' => true, 'class_key' => 'Taxonomy'));
        $c->sortby('menuindex', 'ASC');

        if ($Ts = $this->modx->getCollection('Taxonomy', $c))
        {
            foreach ($Ts as $t)
            {
                $out .= '<fieldset><legend>' . $t->get('pagetitle') . '</legend>';
                $properties = $t->get('properties');
                if ($children = $this->modx->getOption('children', $properties, array()))
                {
                    $out .= $this->getFieldItems($current_values, $children); // 1st time
                }

                $out .= '</fieldset>';
            }
        }
        return $out;
    }

    /**
     * Build the contents of the fieldset -- this is one of those wormhole functions
     * @param $current_values array of term_ids
     * @param $children
     * @param $indent_multiplier integer
     * @param $class
     * @return mixed
     */
    public function getFieldItems($current_values,$children, $indent_multiplier = 0, $class = '')
    {
  
        $out = '';
        foreach ($children as $page_id => $def) {
            $indent = str_repeat('--', $indent_multiplier);
            if (isset($def['published']) && $def['published'])
            {
                $checked = (in_array($page_id,$current_values)) ? ' checked="checked"' : '';
                $out .= $indent . ' <input type="checkbox" name="terms[]" id="terms' . $this->i . '" value="' . $page_id . '"
                    class="multicheck' . $class . '" style=""  '.$checked.'/>
                    <label for="terms' . $this->i . '" class="multichecklabel">' . $def['pagetitle'] . '</label><br/>';
                $this->i = $this->i + 1;
                if (!empty($def['children'])) {
                    $class = $class . ' term' . $page_id;
                    $out .= $this->getFieldItems($current_values,$def['children'], $indent_multiplier+1, $class);
                }
            }
            //$indent_multiplier--;
        }

        return $out;
    }

    /**
     * Get an array of term_ids for any associations with the given page_id
     * @param integer $page_id
     * @return array
     */
    public function getPageTerms($page_id)
    {
        $out = array();
        if ($Terms = $this->modx->getCollection('PageTerm', array('page_id' => $page_id))) {
            foreach ($Terms as $t) {
                $out[] = $t->get('term_id');
            }
        }
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, "pageTerms for page " . $page_id . ': ' . print_r($out, true), '', __CLASS__);
        return $out;
    }

    /**
     * getForm
     *
     * @param integer $page_id current MODX resource
     * @return string HTML form.
     */
    public function getForm($page_id)
    {
        $current_values = $this->getPageTerms($page_id);
        return $this->buildForm($current_values);
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
        $out = '<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Pages which use this Term.</p></div><br>';
        $out .= '<table class="classy term-pages-tbl"><thead><tr>
                <th><strong>'.$this->modx->lexicon('page').'</strong></th>
                <th><strong>'.$this->modx->lexicon('alias').'</strong></th>
            </tr></thead>';
        $c = $this->modx->newQuery('PageTerm');
        $c->where(array('PageTerm.term_id' => $page_id)); // yes, term_id is the current page
        $c->sortby('Page.pagetitle','ASC');

        if ($Terms = $this->modx->getCollectionGraph('PageTerm','{"Page":{}}',$c)) {
            foreach ($Terms as $t) {
                $out .= '<tr>
                    <td><a href="'.MODX_MANAGER_URL.'?a=resource/update&id='.$t->get('page_id') .'">'.$t->Page->get('pagetitle').' ('.$t->get('page_id').')</a></td>
                    <td>'.$t->Page->get('alias').'</td>
                    </tr>';
            }
        }
        else
        {
            return '<div>No pages have been assigned to this Term.</div>';
        }

        $out .'</table>';

        return $out;
    }

    /**
     * Dictate all page terms (array) for the given page_id (int)
     * This forces the modx_page_terms table (PageTerm object) to have the latest info.
     * @param integer $page_id
     * @param array $terms (array of page ids representing terms)
     *
     */
    public function dictatePageTerms($page_id, $terms)
    {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, "Dictating taxonomy term_ids for page " . $page_id . ': ' . print_r($terms, true), '', __CLASS__);
        $existing = $this->modx->getCollection('PageTerm', array('page_id' => $page_id));

        foreach ($existing as $e) {
            if (!in_array($e->get('term_id', $terms))) {
                $e->remove();
            }
        }
        // Reorder
        $seq = 0;
        foreach ($terms as $term_id) {
            if (!$pt = $this->modx->getObject('PageTerm', array('page_id' => $page_id, 'term_id' => $term_id))) {
                $pt = $this->modx->newObject('PageTerm', array('page_id' => $page_id, 'term_id' => $term_id));
            }
            $pt->set('seq', $seq);
            $pt->save();
            $seq++;
        }
    }

    /**
     * Given a possibly deeply nested array, this flattens it to simple key/value pairs
     * @param array $array
     * @param string $prefix (needed for recursion)
     * @return array
     */
    public function flattenArray(array $array, $prefix = '')
    {
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value))
                $result = array_merge($result, $this->flattenArray($value, $prefix . $key . '.'));
            else
                $result[$prefix . $key] = $value;
        }
        return $result;
    }

    /**
     * Gotta look up the URL of our CMP and its actions
     *
     * @param array any optional arguments, e.g. array('action'=>'children','parent'=>123)
     * @return string
     */
    public function getControllerUrl($args=array()) {
        // future: pass as args:
        $namespace='taxonomies';
        $controller='index';
        $url = MODX_MANAGER_URL;
        if ($Action = $this->modx->getObject('modAction', array('namespace'=>$namespace,'controller'=>$controller))) {
            $url .= '?a='.$Action->get('id');
            if ($args) {
                foreach ($args as $k=>$v) {
                    $url.='&'.$k.'='.$v;
                }
            }
        }
        return $url;
    }
}