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
 * [[!getPageTerms? &page_id=`[[+page_id]]` &outerTpl=`sometpl` &innerTpl=`othertpl` &limit=`0`]]
 *
 * @package taxonomies
 **/

$core_path = $modx->getOption('taxonomies.core_path', null, MODX_CORE_PATH.'components/taxonomies/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Taxonomies\Base($modx);
$Snippet->log('getByTerm',$scriptProperties);

$classname = $modx->getOption('classname', $scriptProperties, 'PageTerm');
$term_id = $modx->getOption('term_id', $scriptProperties, $modx->resource->get('id'));
$graph = $modx->getOption('graph', $scriptProperties, '{"Page":{}}');
if ($Pages = $modx->getCollectionGraph($classname, $graph,array('term_id'=>$term_id))) {
    return $Snippet->format($Pages)
}

/*
$scriptProperties['innerTpl'] = $modx->getOption('innerTpl',$scriptProperties, 'ProductTerm');

$moxySnippet = new taxonomies\Snippet($modx);
$out = $moxySnippet->execute('json_product_terms',$scriptProperties);
return $out;
*/