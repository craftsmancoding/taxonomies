<?php
/**
 * @name getTaxonomies
 * @description Returns a list of taxonomies.
 *
 * Available Placeholders
 * ---------------------------------------
 * id, pagetitle
 * use as [[+pagetitle]] on Template Parameters
 * 
 * Parameters
 * -----------------------------
 * @param textfield $outerTpl Format the Outer Wrapper of List (Optional)
 * @param textfield $innerTpl Format the Inner Item of List
 * @param textfield $sort column to sort by default=pagetitle
 * @param textfield $dir sorting direction, ASC or DESC default=ASC
 * @param numberfield $limit the max number of records
 * @param numberfield $start the offset
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * Usage
 * ------------------------------------------------------------
 * [[!getTaxonomies? &outerTpl=`sometpl` &innerTpl=`othertpl` &limit=`0`]]
 *
 * @package taxonomies
 **/

$core_path = $modx->getOption('taxonomies.core_path', null, MODX_CORE_PATH.'components/taxonomies/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Taxonomies\Base($modx);
$Snippet->log('getTaxonomies',$scriptProperties);

$innerTpl = $modx->getOption('innerTpl', $scriptProperties, '<li>[[+pagetitle]]</li>');
$outerTpl = $modx->getOption('outerTpl', $scriptProperties, '<ul>[[+content]]</ul>');
$limit = (int) $modx->getOption('limit', $scriptProperties); 
$start = (int) $modx->getOption('start', $scriptProperties); 
$sort = $modx->getOption('sort', $scriptProperties,'pagetitle');
$dir = $modx->getOption('dir', $scriptProperties,'ASC'); 

$criteria = $modx->newQuery('Taxonomy');
$criteria->where(array('class_key'=>'Taxonomy','published'=>true));

if ($limit) {
    $criteria->limit($limit, $start); 
}
$criteria->sortby($sort,$dir);
$results = $modx->getCollection('Taxonomy',$criteria);

if ($results) {
    return $Snippet->format($results,$innerTpl,$outerTpl);    
}

$modx->log(\modX::LOG_LEVEL_DEBUG, "No results found",'','Snippet getTaxonomies');