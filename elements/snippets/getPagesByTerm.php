<?php
/**
 * @name getPagesByTerm
 * @description Returns a list of pages associated with the given term id OR its children (true hierarchical support)
 * 
 * Set &classname if you have implemented your own join table that joins term_ids with your own objects.
 * Remember that the hierarchical support means that if you search for a term, any page associated with that term
 * OR with that page's children will be included in the result set.
 *
 * Parameters
 * -----------------------------
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param int $term_id (optional: defaults to the current page id)
 * @param bool $exact_matches if true, implied hierarchies are ignored and only pages assigned specifically to term_id will be returned. Default: false
 * @param string $classname Set this if you have created a custom join table used to associate taxonomy terms with something other than pages. (optional: default PageTerm)
 * @param boolean $debug (optional: if set, will output SQL query for debugging)
 *
 * @param int $limit Limit the result
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * Usage
 * ------------------------------------------------------------
 * [[!getPagesByTerm? &term_id=`1` &outerTpl=`sometpl` &innerTpl=`othertpl` &limit=`0`]]
 *
 * @package taxonomies
 **/

$core_path = $modx->getOption('taxonomies.core_path', null, MODX_CORE_PATH.'components/taxonomies/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Taxonomies\Base($modx);
$Snippet->log('getByTerm',$scriptProperties);

$debug = $modx->getOption('debug', $scriptProperties);
$classname = $modx->getOption('classname', $scriptProperties, 'PageTerm');
$term_id = $modx->getOption('term_id', $scriptProperties, $modx->resource->get('id'));
$exact_matches = $modx->getOption('exact_matches', $scriptProperties, false);
$graph = $modx->getOption('graph', $scriptProperties, '{"Page":{}}');
$outerTpl = $modx->getOption('outerTpl',$scriptProperties, '<ul>[[+content]]</ul>');
$innerTpl = $modx->getOption('innerTpl',$scriptProperties, '<li><a href="[[~[[+Page.id]]]]">[[+Page.pagetitle]]</a></li>');

$sort = $modx->getOption('sort', $scriptProperties,'pagetitle');
$dir = $modx->getOption('dir', $scriptProperties,'ASC'); 


if (!$parent = $modx->getObject('modResource', $term_id)) {
    return 'Invalid Term ID.';
}
$filters = array();

// Append children terms if present
$properties = $parent->get('properties');
//return print_r($properties,true);
if (!$exact_matches && isset($properties['children_ids'])) {
    $children_ids = array_keys($properties['children_ids']);
    $children_ids[] = $term_id;
    $filters['term_id:IN'] = $children_ids;
}
else {
    $filters['term_id'] = $term_id;
}

$c = $modx->newQuery($classname);

if (isset($scriptProperties['class_key'])) {
    $filters['Page.class_key'] = $scriptProperties['class_key'];
}
$c->where($filters);

if ($debug) {
    $c->bindGraph($graph);
    $c->prepare();
    $outerTpl = htmlspecialchars($outerTpl);
    $outerTpl = str_replace('[','&#091;',$outerTpl);
    $outerTpl = str_replace(']','&#093;',$outerTpl);
    $innerTpl = htmlspecialchars($innerTpl);
    $innerTpl = str_replace('[','&#091;',$innerTpl);
    $innerTpl = str_replace(']','&#093;',$innerTpl);
    return '<h3>Debug: <code>getPagesByTerm</code></h3><textarea rows="30" cols="60">'.$c->toSQL().'</textarea>
    <h4>Snippet Properties:</h4>
    <strong>classname</strong> <code>'.$classname.'</code><br/>
    <strong>term_id</strong> <code>'.$term_id.'</code><br/>
    <strong>graph</strong> <code>'.$graph.'</code><br/>
    <strong>outerTpl</strong> <code>'.$outerTpl.'</code><br/>
    <strong>innerTpl</strong> <code>'.$innerTpl.'</code><br/>
    ';
}

$c->sortby($sort,$dir);

if ($Pages = $modx->getCollectionGraph($classname, $graph,$c)) {
    return $Snippet->format($Pages,$innerTpl,$outerTpl);
}
else {
    return '<!-- getPagesByTerm no results -->';
}