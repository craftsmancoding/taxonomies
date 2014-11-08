<?php
/**
 * en default topic lexicon file for taxonomies extra
 *
 * Copyright 2013 by Everett Griffiths everett@craftsmancoding.com
 * Created on 07-05-2013
 *
 * taxonomies is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * taxonomies is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * taxonomies; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package taxonomies
 */

// TODO... we got barebones stuff in here until the product settles down enough to translate it.
// Too hard to translate a moving target... stay tuned.
$_lang['taxonomies'] = 'taxonomies'; 
$_lang['taxonomies_desc'] = 'Bringing Taxonomies to MODX Revolution';

$_lang['taxonomy'] = 'Taxonomy';
$_lang['taxonomies'] = 'Taxonomies';
$_lang['pages'] = 'Pages';
$_lang['page'] = 'Page';
$_lang['alias'] = 'Alias';
$_lang['taxonomy_create_here'] = 'Create a Taxonomy Here';
$_lang['taxonomy_new'] = 'New Taxonomy';
$_lang['taxonomy_duplicate'] = 'Duplicate Taxonomy';
$_lang['taxonomy_delete'] = 'Delete Taxonomy';
$_lang['taxonomy_undelete'] = 'Undelete Taxonomy';
$_lang['taxonomy_publish'] = 'Publish Taxonomy';
$_lang['taxonomy_unpublish'] = 'Unpublish Taxonomy';
$_lang['taxonomy_view'] = 'View Taxonomy';

$_lang['term'] = 'Term';
$_lang['terms'] = 'Terms';
$_lang['term_create_here'] = 'Create a Term Here';
$_lang['term_new'] = 'New Term';
$_lang['term_duplicate'] = 'Duplicate Term';
$_lang['term_delete'] = 'Delete Term';
$_lang['term_undelete'] = 'Undelete Term';
$_lang['term_publish'] = 'Publish Term';
$_lang['term_unpublish'] = 'Unpublish Term';
$_lang['term_view'] = 'View Term';

// Settings
$_lang['setting_taxonomies.default_taxonomy_template'] = 'Default Taxonomy Template';
$_lang['setting_taxonomies.default_taxonomy_template_desc'] = 'Which template should be used when creating a new Taxonomy?'
    .'You can use this to reference a Template that contains getTagCloud or getResources or some other menu that will let users navigate the children Terms.';
$_lang['setting_taxonomies.default_term_template'] = 'Default Term Template';
$_lang['setting_taxonomies.default_term_template_desc'] = 'Which template should be used when creating a new Term?'
    .'Your default Term Template is a great place for the getPagesByTerm Snippet.';
$_lang['setting_taxonomies.skip_templates'] = 'Skip Templates';
$_lang['setting_taxonomies.skip_templates_desc'] = 'Define whether the Taxonomies tab should NOT be visible when editing a page that uses one of a list of template ids (comma-separated).';
$_lang['setting_taxonomies.skip_class_keys'] = 'Skip Class Keys';
$_lang['setting_taxonomies.skip_class_keys_desc'] = 'You can refine which types of MODX resources will not display the Taxonomies Tab. '
    .'Include a comma-separated list of class_key values, e.g. modDocument. '
    .'The "Taxonomy" and "Term" classes will never show the Taxonomies Tab.';
