<?php
/**
 * @name getProductTerms
 * @description Returns a list of product_terms.
 * 
 * Available Placeholders
 * ---------------------------------------
 * id, product_id, term_id,term,properties
 * use as [[+term]] on Template Parameters
 * 
 * Parameters
 * -----------------------------
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param int $page_id get terms for this specific page
 * @param int $taxonomy_id limit terms to only this taxonomy
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
$Snippet = new \taxonomies\Snippet($modx);
$Snippet->log('getPageTerms',$scriptProperties);

/*
$scriptProperties['innerTpl'] = $modx->getOption('innerTpl',$scriptProperties, 'ProductTerm');

$moxySnippet = new taxonomies\Snippet($modx);
$out = $moxySnippet->execute('json_product_terms',$scriptProperties);
return $out;
*/