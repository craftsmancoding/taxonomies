<?php
/**
 * Get us started with the most common taxonomies
 */
return array(
    array(
        'pagetitle' => 'Categories',
        'longtitle' => 'Categories',
        'menutitle' => 'Categories',
        'description' => 'All Categories for this Site',
        'alias' => 'categories',
        'uri' => 'categories/',
        'published' => 1,
        'is_folder' => 1,
        'class_key' => 'Taxonomy'
    ),
    array(
        'pagetitle' => 'Tags',
        'longtitle' => 'Tags',
        'menutitle' => 'Tags',
        'description' => 'All Tags for this Site',
        'alias' => 'tags',
        'uri' => 'tags/',
        'published' => 1,
        'is_folder' => 1,
        'class_key' => 'Taxonomy'
    ),
);
/*EOF*/