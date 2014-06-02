<?php
$core_path = $modx->getOption('taxonomies.core_path','',MODX_CORE_PATH.'components/taxonomies/');

// Add the package to the MODX extension_packages array
// TODO: read the table prefix from config
$modx->addExtensionPackage($object['namespace'],"{$core_path}model/", array('tablePrefix'=>'tax_'));
$modx->addPackage('taxonomies',"{$core_path}model/orm/",'tax_');

$manager = $modx->getManager();

// taxonomies Stuff
$manager->createObjectContainer('PageTerm');
$manager->createObjectContainer('PageTaxonomy');
