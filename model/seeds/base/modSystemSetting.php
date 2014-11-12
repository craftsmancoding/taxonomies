<?php
/*-----------------------------------------------------------------
For descriptions here, you must create some lexicon entries:
Name: setting_ + $key
Description: setting_ + $key + _desc
-----------------------------------------------------------------*/
return array(
    // Default Template Used for Taxonomy Pages
    array(
        'key'  =>     'taxonomies.default_taxonomy_template',
		'value'=>     '',
		'xtype'=>     'textfield',
		'namespace' => 'taxonomies',
		'area' => 'taxonomies:default'
    ),
    array(
        'key'  =>     'taxonomies.default_term_template',
        'value'=>     '',
        'xtype'=>     'textfield',
        'namespace' => 'taxonomies',
        'area' => 'taxonomies:default'
    ),
    array(
        'key'  =>     'taxonomies.skip_templates',
		'value'=>     '',
		'xtype'=>     'textfield',
		'namespace' => 'taxonomies',
		'area' => 'taxonomies:default'
    ),
    array(
        'key'  =>     'taxonomies.skip_class_keys',
        'value'=>     '',
        'xtype'=>     'textfield',
        'namespace' => 'taxonomies',
        'area' => 'taxonomies:default'
    ),
    array(
        'key'  =>     'mgr_tree_icon_term',
        'value'=>     'icon-term',
        'xtype'=>     'textfield',
        'namespace' => 'taxonomies',
        'area' => 'taxonomies:default'
    ),
    array(
        'key'  =>     'mgr_tree_icon_taxonomy',
        'value'=>     'icon-taxonomy',
        'xtype'=>     'textfield',
        'namespace' => 'taxonomies',
        'area' => 'taxonomies:default'
    ),
);
/*EOF*/