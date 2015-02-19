<?php
/**
 * @name getPageTerms
 * @description Returns a list of terms (e.g. a tag cloud) for the given page (e.g. resource)
 *
 * Note: this Snippet does not calculate any term hierarchies: it only displays the *exact* terms
 * associated with a given page.
 *
 * Available Placeholders
 * ---------------------------------------
 * term_id, pagetitle, count (Optional)
 * use as [[+term_id]] on Template Parameters
 * 
 * Parameters
 * -----------------------------
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param int $page_id get terms for this specific page
 * @param int $taxonomy_id limit terms to only this taxonomy
 * @param boolean $show_count Include count of items per term (Optional) : If set to `1` will enable [[+count]] placeholder
 * @param int $limit Limit the result, default to 10 : setting it to 0 will show all
 * @param string $placeholder Name of (optional) placeholder to send output to
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

$page_id = $modx->getOption('page_id',$scriptProperties,$modx->resource->get('id'));
$outerTpl = $modx->getOption('outerTpl',$scriptProperties, '<ul>[[+content]]</ul>');
$innerTpl = $modx->getOption('innerTpl',$scriptProperties, '<li><a href="[[~[[+term_id]]]]">[[+pagetitle]]</a></li>');
$limit = $modx->getOption('limit',$scriptProperties,10);
$taxonomy_id = $modx->getOption('taxonomy_id',$scriptProperties,null);
$show_count = $modx->getOption('show_count',$scriptProperties,0);

$limit = ($limit == 0) ? '' : 'LIMIT ' . $limit;
$and_where = (is_null($taxonomy_id)) ? '' : 'AND doc.parent = ' . $taxonomy_id;
$page_id = (is_null($page_id)) ? $modx->resource->get('id') : $page_id;
$placeholder = $modx->getOption('placeholder', $scriptProperties,false);

$content_table = $modx->getTableName('modResource');

if($show_count) {
	$sql = "SELECT terms.term_id, doc.pagetitle, count(doc.id) as count
		FROM tax_page_terms terms
		LEFT JOIN $content_table doc
		ON doc.id = terms.term_id
		LEFT JOIN (
			SELECT subdoc.id, subterms.term_id
			FROM $content_table subdoc
			LEFT JOIN tax_page_terms subterms ON subterms.page_id = subdoc.id
		) subq
		ON subq.term_id = terms.term_id
		WHERE terms.page_id = {$page_id} {$and_where} 
		GROUP BY doc.id
		ORDER BY doc.pagetitle ASC {$limit};";
} else {
	$sql = "SELECT terms.term_id,doc.pagetitle
		FROM tax_page_terms terms
		LEFT JOIN $content_table doc ON doc.id = terms.term_id
		WHERE terms.page_id = {$page_id} {$and_where} ORDER BY doc.pagetitle ASC {$limit};";
}

$obj = $modx->query($sql);
$results = $obj->fetchAll(PDO::FETCH_ASSOC);

if(count($results) == 0) {
	$modx->log(\modX::LOG_LEVEL_DEBUG, "No results found",'','getPageTerms',__LINE__);
}

$s = $Snippet->format($results,$innerTpl,$outerTpl);
if($placeholder){
	$modx->setPlaceholder($placeholder, $s);
	return '';
} else {
	return $s;
}
