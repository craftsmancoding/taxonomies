<?php
$xpdo_meta_map['ProductTerm']= array (
  'package' => 'taxonomies',
  'version' => '1.0',
  'table' => 'product_terms',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'page_id' => NULL,
    'term_id' => NULL,
  ),
  'fieldMeta' => 
  array (
    'page_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'term_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'columns' => 
      array (
        'id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'pageterm' => 
    array (
      'alias' => 'pageterm',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'page_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'term_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Term' => 
    array (
      'class' => 'Term',
      'local' => 'term_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Page' => 
    array (
      'class' => 'modResource',
      'local' => 'page_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
