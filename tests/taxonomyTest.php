<?php

/**
 * Before running these tests, you must install the package using Repoman:
 * https://github.com/craftsmancoding/repoman
 *
 *
 * To run these tests, pass the test directory as the 1st argument to phpunit:
 *
 *   phpunit path/to/taxonomies/tests
 *
 * or if you're having any trouble running phpunit, download its .phar file, and
 * then run the tests like this:
 *
 *  php phpunit.phar path/to/taxonomies/tests
 *
 * See http://forums.modx.com/thread/91009/xpdo-validation-rules-executing-prematurely#dis-post-498398
 *
 * WARNING: these tests DO hit the MODX database! Running these tests lots of time will increment the primary key
 * on the modx_site_content table.
 *
 * REMEMBER: When testing snippets, you must set "$modx" as a global variable because that's the variable
 * the Snippets are looking for.
 */
class taxonomyTest extends \PHPUnit_Framework_TestCase
{

    // Must be static because we set it up inside a static function
    public static $modx;

    public static $Tax;
    public static $Term;
    public static $P; // Pages
    public static $PageTerm; // PageTerms
    public static $Newterm;

    public static function setUpBeforeClass()
    {

        self::$modx = new \modX();
        self::$modx->initialize('mgr');
        //self::$modx->setLogTarget('ECHO');
        //self::$modx->setLogLevel(4);
        $core_path = self::$modx->getOption('taxonomies.core_path', '', MODX_CORE_PATH . 'components/taxonomies/');
        self::$modx->addExtensionPackage('taxonomies', "{$core_path}model/", array('tablePrefix' => 'tax_'));

        self::_deleteTestResources();
    }

    /**
     * Prepare MODX for our tests.
     *
     * First we set up a folder structure like this, including a test taxonomy, a few terms, and some
     * pages that we will assign to the taxonomy.
     *
     * Page 1
     * Page 2
     * Page 3
     * Test Taxonomy
     *      +----- Test Term A
     *      +----- Test Term B
     *      +----- Test Term C
     *
     * We also make the following page + term associations:
     *  Page 1: Term A, Term B
     *
     * NOW LET THE TESTS BEGIN!!!
     */
    //public static function setUpBeforeClass() {
    public function setUp()
    {

        // Prep: create a parent taxonomy (a container of terms)
        if (!self::$Tax = self::$modx->getObject('Taxonomy', array('alias' => 'test-taxonomy'))) {
            self::$Tax = self::$modx->newObject('Taxonomy');
            self::$Tax->fromArray(array(
                'pagetitle' => 'Test Taxonomy',
                'longtitle' => 'Test Taxonomy',
                'menutitle' => 'Test Taxonomy',
                'alias' => 'test-taxonomy',
                'uri' => 'test-taxonomy/',
                'class_key' => 'Taxonomy',
                'isfolder' => 1,
                'published' => 1
            ));
            self::$Tax->save();
        }
        // Load terms        
        if (!self::$Term['a'] = self::$modx->getObject('Term', array('alias' => 'test-term-a'))) {
            self::$Term['a'] = self::$modx->newObject('Term');
            self::$Term['a']->fromArray(array(
                'pagetitle' => 'Test Term A',
                'alias' => 'test-term-a',
                'class_key' => 'Term',
                'isfolder' => 1,
                'published' => 1
            ));
        }
        self::$Term['a']->set('parent', self::$Tax->get('id'));
        self::$Term['a']->save();

        if (!self::$Term['b'] = self::$modx->getObject('Term', array('alias' => 'test-term-b'))) {
            self::$Term['b'] = self::$modx->newObject('Term');
            self::$Term['b']->fromArray(array(
                'pagetitle' => 'Test Term B',
                'alias' => 'test-term-b',
                'class_key' => 'Term',
                'isfolder' => 1,
                'published' => 1
            ));
        }
        self::$Term['b']->set('parent', self::$Tax->get('id'));
        self::$Term['b']->save();

        if (!self::$Term['c'] = self::$modx->getObject('Term', array('alias' => 'test-term-c'))) {
            self::$Term['c'] = self::$modx->newObject('Term');
            self::$Term['c']->fromArray(array(
                'pagetitle' => 'Test Term C',
                'alias' => 'test-term-c',
                'class_key' => 'Term',
                'isfolder' => 1,
                'published' => 1
            ));
        }
        self::$Term['c']->set('parent', self::$Tax->get('id'));
        self::$Term['c']->save();

        // Some pages for testing associations
        if (!self::$P['1'] = self::$modx->getObject('modResource', array('alias' => 'test-page1'))) {
            self::$P['1'] = self::$modx->newObject('modResource');
            self::$P['1']->fromArray(array(
                'pagetitle' => 'Test Page 1',
                'alias' => 'test-page1',
                'published' => 1
            ));
            self::$P['1']->save();
        }

        if (!self::$P['2'] = self::$modx->getObject('modResource', array('alias' => 'test-page2'))) {
            self::$P['2'] = self::$modx->newObject('modResource');
            self::$P['2']->fromArray(array(
                'pagetitle' => 'Test Page 2',
                'alias' => 'test-page2',
                'published' => 1
            ));
            self::$P['2']->save();
        }

        if (!self::$P['3'] = self::$modx->getObject('modResource', array('alias' => 'test-page3'))) {
            self::$P['3'] = self::$modx->newObject('modResource');
            self::$P['3']->fromArray(array(
                'pagetitle' => 'Test Page 3',
                'alias' => 'test-page3',
                'published' => 1
            ));
            self::$P['3']->save();
        }

        // Then we associate pages with terms
        if (!self::$PageTerm['1'] = self::$modx->getObject('PageTerm', array('term_id' => self::$Term['a']->get('id'), 'page_id' => self::$P['1']->get('id')))) {
            self::$PageTerm['1'] = self::$modx->newObject('PageTerm');
            self::$PageTerm['1']->fromArray(array(
                'page_id' => self::$P['1']->get('id'),
                'term_id' => self::$Term['a']->get('id'),
                'seq' => 0
            ));
            self::$PageTerm['1']->save();
        }

        if (!self::$PageTerm['2'] = self::$modx->getObject('PageTerm', array('term_id' => self::$Term['b']->get('id'), 'page_id' => self::$P['1']->get('id')))) {
            self::$PageTerm['2'] = self::$modx->newObject('PageTerm');
            self::$PageTerm['2']->fromArray(array(
                'page_id' => self::$P['1']->get('id'),
                'term_id' => self::$Term['b']->get('id'),
                'seq' => 0
            ));
            self::$PageTerm['2']->save();
        }

    }

    /**
     * Remove all our shiz
     */
    public static function tearDownAfterClass()
    {
        self::_deleteTestResources();

        self::$modx->setLogTarget('FILE');
        self::$modx->setLogLevel(1);

        return;
    }

    private static function _deleteTestResources()
    {
        //return;
        print "Cleaning out test resources..............\n";
        $aliases = array(
            'test-term-a','test-term-b','test-term-c','test-term-e','test-page1','test-page2','test-page3','test-taxonomy'
        );
        foreach($aliases as $a)
        {
            if ($R = self::$modx->getObject('modResource', array('alias'=>$a)))
            {
                if ($PT = self::$modx->getCollection('PageTerm', array('page_id'=>$R->get('id'))))
                {
                    foreach($PT as $p)
                    {
                        $p->remove();
                    }
                }
                $R->remove();
            }
        }
        return;
    }
//
//    private function _resetTermParent()
//    {
//        // Make sure the terms are all put back under the parent tax
//        self::$Term['a']->set('parent', self::$Tax->get('id'));
//        self::$Term['a']->save();
//        self::$Term['b']->set('parent', self::$Tax->get('id'));
//        self::$Term['b']->save();
//        self::$Term['c']->set('parent', self::$Tax->get('id'));
//        self::$Term['c']->save();
//    }


    /**
     * Basic Stuff: ensure our setup worked
     */
    public function testSetup()
    {
        $pageTerms = self::$modx->getCollection('PageTerm', array('page_id' => self::$P['1']->get('id')));
        $page_ids = array();
        $term_ids = array();
        foreach ($pageTerms as $pt) {
            $page_ids[] = $pt->get('page_id');
            $term_ids[] = $pt->get('term_id');
        }

        $this->assertTrue(in_array(self::$P['1']->get('id'), $page_ids));
        $this->assertTrue(in_array(self::$Term['a']->get('id'), $term_ids));
        $this->assertTrue(in_array(self::$Term['b']->get('id'), $term_ids));

    }

    /**
     * The trickiest thing here is how we track and update the parent taxonomy properties
     * and the hierarchical terms.
     *
     * Taxonomy Properties
     * {
     * "children":{
     * "225":{
     * "alias":"test-term-3",
     * "pagetitle":"Term 3",
     * "published":true,
     * "menuindex":0,
     * "children":[]
     * }
     * },
     * "children_ids":{
     * "225":true
     * }
     * }
     *
     * Term Properties (simple, no children):
     * {
     * "fingerprint":"f391e15428e162fe2c1744f5359490d4",
     * "prev_parent":224
     * }
     *
     * Term Properties (hierarchical, with children):
     * {
     * "fingerprint":"1e735c4df641479da517f3b3fdd06266",
     * "prev_parent":220,
     * "children":{
     * "223":{
     * "alias":"test-term-1",
     * "pagetitle":"Term 1",
     * "published":true,
     * "menuindex":0,"children":[]
     * }
     * },
     * "children_ids":{
     * "223":true
     * }
     * }

     */

//    public function testTaxonomyProperties()
//    {
//
//        $p = self::$Tax->get('properties');
//        $this->assertTrue(!empty($p));
//        $this->assertTrue(isset($p['children_ids']));
//        $this->assertTrue(isset($p['children']));
//        $this->assertTrue(is_array($p['children']));
//        $this->assertTrue($p['children_ids'][self::$Term['a']->get('id')]);
//        $this->assertTrue($p['children_ids'][self::$Term['b']->get('id')]);
//        $this->assertTrue($p['children_ids'][self::$Term['c']->get('id')]);
//
//        // Any extra children in there?
//        foreach ($p['children'] as $term_id => $tdata)
//        {
//            $this->assertTrue((bool) $term_id);
//
//        }
//        foreach ($p['children'] as $term_id => $tdata)
//        {
//            $T = self::$modx->getObject('Term', $term_id);
//            $this->assertTrue(!empty($T));
//            $this->assertEquals($T->get('pagetitle'), $tdata['pagetitle']);
//            $tprops = $T->get('properties');
//            $this->assertTrue(empty($tprops['children'])); // no sub-children yet
//        }
//
//    }


    public function testMoveTerm()
    {
        /*        self::$modx->setLogTarget('ECHO');
                self::$modx->setLogLevel(4);

                $this->_resetTermParent();

                // Make into a hierarchy: A --> B
                self::$Term['b']->set('parent',self::$Term['a']->get('id'));
                self::$Term['b']->save();

                // This should be unchanged:
                $p = self::$Tax->get('properties');
                $this->assertTrue(isset($p['children_ids']));
                $this->assertTrue(isset($p['children']) && is_array($p['children']));
                $this->assertTrue($p['children_ids'][ self::$Term['a']->get('id') ]);
                $this->assertTrue($p['children_ids'][ self::$Term['b']->get('id') ]);
                $this->assertTrue($p['children_ids'][ self::$Term['c']->get('id') ]); */


        /*
                // Make into a hierarchy: A --> B --> C
                self::$Term['c']->set('parent',self::$Term['b']->get('id'));
                self::$Term['c']->save();
                self::$Term['b']->set('parent',self::$Term['a']->get('id'));
                self::$Term['b']->save();

                // This should be unchanged:
                $p = self::$Tax->get('properties');
                $this->assertTrue(isset($p['children_ids']));
                $this->assertTrue(isset($p['children']) && is_array($p['children']));
                $this->assertTrue($p['children_ids'][ self::$Term['a']->get('id') ]);
                $this->assertTrue($p['children_ids'][ self::$Term['b']->get('id') ]);
                $this->assertTrue($p['children_ids'][ self::$Term['c']->get('id') ]);
        */


    }

    /**
     * Test the testGetPageTerms Snippet
     *
     */
    public function testFirstGetPageTerms()
    {


        // You MUST set $modx as a global variable, or runSnippet will encounter errors!
        // You have to do this for EACH test function when you are testing a Snippet!
        global $modx;
        $modx = self::$modx;
        $props = array();
        $props['taxonomy_id'] = self::$Tax->get('id');
        $props['innerTpl'] = '<li>[[+term_id]] : [[+pagetitle]]</li>';
        $props['outerTpl'] = '<ul>[[+content]]</ul>';
        $props['page_id'] = self::$P['1']->get('id');

        $actual = self::$modx->runSnippet('getPageTerms', $props);

        $expected = '<ul><li>' . self::$Term['a']->get('id') . ' : Test Term A</li><li>' . self::$Term['b']->get('id') . ' : Test Term B</li></ul>';
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test the testGetPageTerms Snippet
     *
     */
    public function testGetPageTermsLimit()
    {
        // You MUST set $modx as a global variable, or runSnippet will encounter errors!
        // You have to do this for EACH test function when you are testing a Snippet!
        global $modx;
        $modx = self::$modx;
        $props = array();
        $props['taxonomy_id'] = self::$Tax->get('id');
        $props['innerTpl'] = '<li>[[+term_id]] : [[+pagetitle]]</li>';
        $props['outerTpl'] = '<ul>[[+content]]</ul>';
        $props['page_id'] = self::$P['1']->get('id');
        $props['limit'] = 1;

        $actual = self::$modx->runSnippet('getPageTerms', $props);
        $expected = '<ul><li>' . self::$Term['a']->get('id') . ' : Test Term A</li></ul>';
        $this->assertEquals($expected, $actual);
    }

    public function testSnippetExists()
    {
        $S = self::$modx->getObject('modSnippet', array('name'=>'getTagCloud'));
        $this->assertTrue(!is_null($S));
    }

    public function testFirstGetTagCloud()
    {
        // You MUST set $modx as a global variable, or runSnippet will encounter errors!
        // You have to do this for EACH test function when you are testing a Snippet!
        global $modx;
        $modx = self::$modx;
        $props = array();
        $props['innerTpl'] = '<li>[[+pagetitle]] <strong>([[+count]])</strong></li>';
        $props['outerTpl'] = '<ul>[[+content]]</ul>';
        $actual = self::$modx->runSnippet('getTagCloud', $props);
        $expected = '<ul><li>Test Term A <strong>(1)</strong></li><li>Test Term B <strong>(1)</strong></li></ul>';
        $this->assertEquals($expected, $actual);
    }

    public function testGetTagCloudIncludeEmpty()
    {
        // You MUST set $modx as a global variable, or runSnippet will encounter errors!
        // You have to do this for EACH test function when you are testing a Snippet!
        global $modx;
        $modx = self::$modx;
        $props = array();
        $props['includeEmpty'] = 1;
        $props['innerTpl'] = '<li>[[+pagetitle]] <strong>([[+count]])</strong></li>';
        $props['outerTpl'] = '<ul>[[+content]]</ul>';
        $actual = self::$modx->runSnippet('getTagCloud', $props);
        $expected = '<ul><li>Test Term A <strong>(1)</strong></li><li>Test Term B <strong>(1)</strong></li><li>Test Term C <strong>(0)</strong></li></ul>';

        $this->assertTrue( (bool) strpos ($actual, '<li>Test Term A <strong>(1)</strong></li>') );
        $this->assertTrue( (bool) strpos ($actual, '<li>Test Term B <strong>(1)</strong></li>') );
        $this->assertTrue( (bool) strpos ($actual, '<li>Test Term C <strong>(0)</strong></li>') );
    }

    public function testCalcFingerprint()
    {
        $Term = self::$modx->newObject('Term');
        $actual = $Term->calcFingerprint();
        $this->assertEquals('470cd69788e6cb59156e060eef3022cf',$actual);

        $Term = self::$modx->newObject('Term',array('pagetitle'=>'Not Nothing'));
        $actual = $Term->calcFingerprint();
        $this->assertNotEquals('470cd69788e6cb59156e060eef3022cf',$actual);

        $Term = self::$modx->newObject('Term',array('menuindex'=>2));
        $actual = $Term->calcFingerprint();
        $this->assertNotEquals('470cd69788e6cb59156e060eef3022cf',$actual);

        $Term = self::$modx->newObject('Term',array('alias'=>'something'));
        $actual = $Term->calcFingerprint();
        $this->assertNotEquals('470cd69788e6cb59156e060eef3022cf',$actual);

        $Term = self::$modx->newObject('Term',array('parent'=>2));
        $actual = $Term->calcFingerprint();
        $this->assertNotEquals('470cd69788e6cb59156e060eef3022cf',$actual);

        $Term = self::$modx->newObject('Term',array('properties'=>array('children'=>array(123=>true))));
        $actual = $Term->calcFingerprint();
        $this->assertNotEquals('470cd69788e6cb59156e060eef3022cf',$actual);
    }

    /**
     * Creating a new term should ripple up the hierarchy.
     *
     */
    public function testCreateNewTermUnderTerm()
    {
        $parent_id = self::$Term['a']->get('id');
        $this->assertTrue((bool) self::$Term['a']->get('id'));

        self::$Newterm = self::$modx->newObject('Term');
        self::$Newterm->fromArray(array(
            'pagetitle' => 'Test Term D',
            'alias' => 'test-term-d',
            'class_key' => 'Term',
            'isfolder' => 1,
            'published' => 1,
            'parent' => self::$Term['a']->get('id')
        ));

        $result = self::$Newterm->save();
        $this->assertTrue((bool) $result);
        $newterm_id = self::$Newterm->get('id');
        $this->assertTrue((bool) $newterm_id);

        // Verify the immediate parent (Term A)
        $Parent = self::$modx->getObject('modResource', $parent_id);
        $this->assertTrue(!empty($Parent));
        $properties = $Parent->get('properties');
        $this->assertTrue(is_array($properties));
        $this->assertTrue(!empty($properties));
        $this->assertTrue(isset($properties['children']));
        $this->assertTrue(isset($properties['children_ids']));
        $this->assertTrue(isset($properties['children'][$newterm_id]));
        $this->assertTrue(isset($properties['children_ids'][$newterm_id]));
        $this->assertTrue($properties['children_ids'][$newterm_id]);

        // Verify the grandparent (Test Taxonomy)
        $parent_id = $Parent->get('parent');
        $this->assertTrue((bool) $parent_id);
        $Parent = self::$modx->getObject('modResource', $parent_id);
        $this->assertTrue(!empty($Parent));
        $properties = $Parent->get('properties');
        $this->assertTrue(is_array($properties));
        $this->assertTrue(!empty($properties));
        $this->assertTrue(isset($properties['children']));
        $this->assertTrue(isset($properties['children_ids']));
        //$this->assertTrue(isset($properties['children'][$newterm_id]));
        $this->assertTrue(isset($properties['children_ids'][$newterm_id]));
        $this->assertTrue($properties['children_ids'][$newterm_id]);

        $children_ids = self::$modx->getOption('children_ids',$properties,array());

        $this->assertTrue(!empty($children_ids));
        $this->assertTrue(isset($children_ids[self::$Newterm->get('id')]));

        $cnt = count(array_keys($children_ids));
        $this->assertEquals(4,$cnt);

        // Test Delete
        $result = self::$Newterm->remove();
        $this->assertTrue((bool) $result);
    }

    public function testDeleteTerm()
    {
        // Verify Setup...
        $parent_id = self::$Tax->get('id');
        $Parent = self::$modx->getObject('modResource', $parent_id);
        $this->assertTrue(!empty($Parent));

        $p = $Parent->get('properties');
        // All there?
        //print 'Parent properties ('.$Parent->get('id').'):';
        //print_r($p);

        $this->assertTrue(isset($p['children']));
        $this->assertTrue($p['children_ids'][self::$Term['a']->get('id')]);
        $this->assertTrue($p['children_ids'][self::$Term['b']->get('id')]);
        $this->assertTrue($p['children_ids'][self::$Term['c']->get('id')]);

        $this->assertTrue(isset($p['children'][self::$Term['a']->get('id')]));
        $this->assertTrue(isset($p['children'][self::$Term['b']->get('id')]));
        $this->assertTrue(isset($p['children'][self::$Term['c']->get('id')]));

        $this->assertEquals(self::$Term['b']->get('parent'), $parent_id);

        self::$modx->setLogTarget('ECHO');
        self::$modx->setLogLevel(4);

        $term_b_id = self::$Term['b']->get('id');
        $result = self::$Term['b']->remove(); // <--- Delete!!!  The big event!!!
        $this->assertTrue($result);


        $B = self::$modx->getObject('modResource', $term_b_id);
        $this->assertTrue(is_null($B),'Term B should be deleted.');

        $Parent = self::$modx->getObject('modResource', $parent_id);

        $this->assertTrue(!empty($Parent),'The Parent Taxonomy should still exist.');
        $properties = $Parent->get('properties');

        $this->assertTrue(is_array($properties),'Properties shoudl always be an array.');

        $this->assertTrue(isset($properties['children_ids']),'The "children_ids" key should exist within the properties.');

        // Here was a fierce battle.... R.I.P. 8 hours and 42 minutes of my life.
        //print 'Term B: '.$term_b_id."\n";
        //print "Properties:\n";
        //print_r($properties);
        //return;
        $this->assertFalse(isset($properties['children'][$term_b_id]),"Term B's spot in children should have been removed.");
        $this->assertFalse(isset($properties['children_ids'][$term_b_id]),"Term B's spot in children_ids should have been removed.");
    }

    public function testRippleUpModificationsToChildrenIds()
    {

        $term_b_id = self::$Term['b']->get('id');
        self::$Term['b']->remove();

        $Parent = self::$modx->getObject('modResource', self::$Tax->get('id'));

        $this->assertTrue(!empty($Parent));
        $properties = $Parent->get('properties');
        $this->assertTrue(is_array($properties));
        $this->assertTrue(isset($properties['children_ids']));
        $this->assertFalse(isset($properties['children_ids'][$term_b_id]));

        $Term = self::$modx->newObject('Term');
        $Term->fromArray(array(
            'pagetitle' => 'Test Term E',
            'alias' => 'test-term-e',
            'class_key' => 'Term',
            'isfolder' => 1,
            'published' => 1,
            'parent' => self::$Term['a']->get('id')
        ));
        $result = $Term->save();
        $term_id = $Term->get('id');
        $this->assertTrue((bool) $result, 'Term failed to save.');
        $this->assertTrue((bool) $Term->get('id'),'New term did not get an id.');


        // Check the parent term
        $Parent = self::$modx->getObject('modResource', self::$Term['a']->get('id'));
        $properties = $Parent->get('properties');
        $this->assertTrue(is_array($properties));
        $this->assertTrue(isset($properties['children_ids']));
        $this->assertTrue(isset($properties['children_ids'][$term_id]));

        // Check the taxonomy
        $Parent = self::$modx->getObject('modResource', self::$Tax->get('id'));
        $this->assertTrue(!empty($Parent));
        $properties = $Parent->get('properties');
        $this->assertTrue(is_array($properties));
        $this->assertTrue(isset($properties['children_ids']));
        $this->assertTrue(isset($properties['children_ids'][$term_id]));

        // Delete the Term, verify that it's gone
        $Term->remove();

        // Check the parent term
        $Parent = self::$modx->getObject('modResource', self::$Term['a']->get('id'));
        $properties = $Parent->get('properties');
        $this->assertTrue(is_array($properties));
        $this->assertFalse(isset($properties['children_ids'][$term_id]));

        // Check the taxonomy
        $Parent = self::$modx->getObject('modResource', self::$Tax->get('id'));
        $properties = $Parent->get('properties');
        $this->assertFalse(isset($properties['children_ids'][$term_id]));

    }

    public function testFlattenChildrenObjectsToIds()
    {
        $Term = self::$modx->newObject('Term');
        $sample = array(
            '123' => 'gnar',
            '456' => 'stuff'
        );

        $term_ids = $Term->flattenChildrenObjectsToIds($sample);

        $this->assertEquals(2, count(array_keys($term_ids)));
        $this->assertTrue(isset($term_ids['123']));
        $this->assertTrue(isset($term_ids['456']));

        $sample = array(
            '123' => 'gnar',
            '456' => 'stuff',
            '789' => array(
                'children' => array(
                    '666' => 'other_stuff'
                )
            )
        );

        $term_ids = $Term->flattenChildrenObjectsToIds($sample);

        $this->assertEquals(4, count(array_keys($term_ids)));
        $this->assertTrue(isset($term_ids['123']));
        $this->assertTrue(isset($term_ids['456']));
        $this->assertTrue(isset($term_ids['789']));
        $this->assertTrue(isset($term_ids['666']));

    }

    public function testGetTaxonomiesAndTerms()
    {

    }

}
