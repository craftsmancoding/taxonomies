<?php
/**
 * @name getPageTerms
 * @description Returns a list of terms (e.g. a tag cloud) for the given page (e.g. resource)
 * 
 * Available Placeholders
 * ---------------------------------------
 * term_id, pagetitle
 * use as [[+term_id]] on Template Parameters
 * 
 * Parameters
 * -----------------------------
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param int $page_id get terms for this specific page
 * @param int $taxonomy_id limit terms to only this taxonomy
 * @param int $limit Limit the result, default to 10 : setting it to 0 will show all
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
$Snippet->log('getPageTerms',$scriptProperties);


$page_id = $modx->getOption('page_id',$scriptProperties,null);
$outerTpl = $modx->getOption('outerTpl',$scriptProperties, '<ul>[[+content]]</ul>');
$innerTpl = $modx->getOption('innerTpl',$scriptProperties, '<li>[[+term_id]] : [[+pagetitle]]</li>');
$limit = $modx->getOption('limit',$scriptProperties,10);
$taxonomy_id = $modx->getOption('taxonomy_id',$scriptProperties,null);

$limit = ($limit == 0) ? '' : 'LIMIT ' . $limit;
$and_where = (is_null($taxonomy_id)) ? '' : 'AND doc.parent = ' . $taxonomy_id;
$page_id = (is_null($page_id)) ? $modx->resource->get('id') : $page_id;

$sql = "SELECT terms.term_id,doc.pagetitle
		FROM tax_page_terms terms
		LEFT JOIN modx_site_content doc ON doc.id = terms.term_id
		WHERE terms.page_id = {$page_id} {$and_where} ORDER BY terms.term_id ASC {$limit};";

$obj = $modx->query($sql);
$results = $obj->fetchAll(PDO::FETCH_ASSOC);

if(count($results) == 0) {
	$modx->log(\modX::LOG_LEVEL_DEBUG, "No results found",'','getPageTerms',__LINE__);
}

return $Snippet->format($results,$innerTpl,$outerTpl);