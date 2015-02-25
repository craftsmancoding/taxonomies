<?php
/**
 * The menu 'text' and 'description' fields should be Lexicon keys.
 *
 *
 */
return array(
    array(
        'text' => 'taxonomies',
        'description' => 'taxonomies_desc',
        'parent' => 'components',
        'action' => 0,
        'icon' => '',
        'menuindex' => 0,
        'params' => '',
        'handler' => '',
        'permissions' => '',
        'Action' => array (
            'namespace' => 'taxonomies',
            'controller' => 'index',
            'haslayout' => 1,
            'lang_topics' => 'taxonomies:default',
            'assets' => '',
            'help_url' =>  '',
        ),
    ),
);
/*EOF*/