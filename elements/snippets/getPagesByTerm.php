<?php
/**
 * @name getPagesByTerm
 * @description Returns a list of pages associated with the given term id
 * 
 * classname can be anything that m 
 * Parameters
 * -----------------------------
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param int $term_id (optional: defaults to the current page id)
 * @param string $classname (optional: default PageTerm)
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
$graph = $modx->getOption('graph', $scriptProperties, '{"Page":{}}');
$outerTpl = $modx->getOption('outerTpl',$scriptProperties, '<ul>[[+content]]</ul>');
$innerTpl = $modx->getOption('innerTpl',$scriptProperties, '<li>[[+pagetitle]]</li>');

$c = $modx->newQuery($classname);
$filters = array();
$filters['term_id'] = $term_id;
if (isset($scriptProperties['class_key'])) {
    $filters['Page.class_key'] = $scriptProperties['class_key'];
}
$c->where($filters);

if ($debug) {
    $c->bindGraph($graph);
    $c->prepare();
    return '<textarea rows="20" cols="40">'.$c->toSQL().'</textarea>';
}

if ($Pages = $modx->getCollectionGraph($classname, $graph,$c)) {
    return $Snippet->format($Pages,$innerTpl,$outerTpl);
}
else {
    return '<!-- getPagesByTerm no results -->';
}