<?php
/**
 * @name getTagCloud
 * @description Returns a list of terms and count how many pages are in each one.
 * 
 * Available Placeholders
 * ---------------------------------------
 * id, pagetitle, count
 * use as [[+id]] on Template Parameters
 * 
 * Parameters
 * -----------------------------
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param string $sort column: pagetitle, id, count
 * @param string $dir ASC | DESC
 * @param int $limit Limit the result, default to 10 : setting it to 0 will show all
 * @param boolean $includeEmpty include all terms (disregard if it's assigned to certain page)
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * Usage
 * ------------------------------------------------------------
 * [[!getTagCloud?  &outerTpl=`sometpl` &innerTpl=`othertpl`]]
 *
 * @package taxonomies
 */

$core_path = $modx->getOption('taxonomies.core_path', null, MODX_CORE_PATH.'components/taxonomies/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Taxonomies\Base($modx);
$Snippet->log('getTagCloud',$scriptProperties);

$includeEmpty = $modx->getOption('includeEmpty',$scriptProperties, 0);
$sort = $modx->getOption('sort',$scriptProperties, 0);
$dir = $modx->getOption('dir',$scriptProperties, 'DESC');
$outerTpl = $modx->getOption('outerTpl',$scriptProperties, '<ul>[[+content]]</ul>');
$innerTpl = $modx->getOption('innerTpl',$scriptProperties, '<li><a href="[[~[[+id]]]]">[[+pagetitle]]</a> <strong>([[+count]])</strong></li>');

$limit = $modx->getOption('limit',$scriptProperties, 0);

$content_table = $modx->getTableName('modResource');
$pageterms_table = $modx->getTableName('PageTerm');
if($includeEmpty == 1)
{
	$sql = "SELECT doc.id as id, doc.pagetitle, count(terms.page_id) as count
		FROM $content_table doc
		LEFT JOIN $pageterms_table terms ON doc.id = terms.term_id
		WHERE doc.class_key='Term'
		GROUP BY doc.id";
}
else
{
	$sql = "SELECT terms.term_id as id, doc.pagetitle, count(*) as count
		FROM $content_table doc
		JOIN $pageterms_table terms ON terms.term_id = doc.id
		WHERE doc.class_key='Term'
		GROUP BY terms.term_id";
}

if($sort) {
	$sql.=" ORDER BY $sort $dir";
}
if($limit) {
	$sql.=" LIMIT $limit";
}

$obj = $modx->query($sql);
$results = $obj->fetchAll(PDO::FETCH_ASSOC);

if(count($results) == 0)
{
	$modx->log(\modX::LOG_LEVEL_DEBUG, "No results found",'','getTagCloud',__LINE__);
}

return $Snippet->format($results,$innerTpl,$outerTpl);
