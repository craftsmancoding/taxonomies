<?php
/**
 * Before running these tests, you must install the package using Repoman
 * and seed the database with the test data!
 *
 *  php repoman.php install /path/to/repos/moxycart '--seed=base,test'
 * 
 * That will ensure that the database tables contain the correct test data. 
 * If you need to create more test data, make sure you add the appropriate 
 * arrays to the model/seeds/test directory (either manually or via repoman's
 * export command).
 *
 * To run these tests, pass the test directory as the 1st argument to phpunit:
 *
 *   phpunit path/to/moxycart/core/components/moxycart/tests
 *
 * or if you're having any trouble running phpunit, download its .phar file, and 
 * then run the tests like this:
 *
 *  php phpunit.phar path/to/moxycart/core/components/moxycart/tests
 *
 * See http://forums.modx.com/thread/91009/xpdo-validation-rules-executing-prematurely#dis-post-498398 
 */

class taxonomyTest extends \PHPUnit_Framework_TestCase {

    // Must be static because we set it up inside a static function
    public static $modx;

    public static $Tax; 
    public static $Term;
    public static $P; // Pages
    public static $PageTerm; // PageTerms
            
    /**
     * Load up MODX for our tests.
     *
     */
    public static function setUpBeforeClass() {        
        self::$modx = new \modX();
        self::$modx->initialize('mgr');
        $core_path = self::$modx->getOption('moxycart.core_path','',MODX_CORE_PATH.'components/moxycart/');
        self::$modx->addExtensionPackage('moxycart',"{$core_path}model/orm/", array('tablePrefix'=>'moxy_'));
        self::$modx->addPackage('moxycart',"{$core_path}model/orm/",'moxy_');
        self::$modx->addPackage('foxycart',"{$core_path}model/orm/",'foxy_');

        // Prep: create a parent store 
        if (!self::$Tax = self::$modx->getObject('Taxonomy', array('alias'=>'test-taxonomy'))) {
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
        if (!self::$Term['a'] = self::$modx->getObject('Term', array('alias'=>'test-term-a'))) {
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
             
        if (!self::$Term['b'] = self::$modx->getObject('Term', array('alias'=>'test-term-b'))) {
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
        
        if (!self::$Term['c'] = self::$modx->getObject('Term', array('alias'=>'test-term-c'))) {
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
        if (!self::$P['1'] = self::$modx->getObject('modResource', array('alias'=>'test-page1'))) {
            self::$P['1'] = self::$modx->newObject('modResource');
            self::$P['1']->fromArray(array(
                'pagetitle' => 'Test Page 1',
                'alias' => 'test-page1',
                'published' => 1
            ));
            self::$P['1']->save();        
        }

        if (!self::$P['2'] = self::$modx->getObject('modResource', array('alias'=>'test-page2'))) {
            self::$P['2'] = self::$modx->newObject('modResource');
            self::$P['2']->fromArray(array(
                'pagetitle' => 'Test Page 2',
                'alias' => 'test-page2',
                'published' => 1
            ));
            self::$P['2']->save();        
        }
        
        if (!self::$P['3'] = self::$modx->getObject('modResource', array('alias'=>'test-page3'))) {
            self::$P['3'] = self::$modx->newObject('modResource');
            self::$P['3']->fromArray(array(
                'pagetitle' => 'Test Page 3',
                'alias' => 'test-page3',
                'published' => 1
            ));
            self::$P['3']->save();        
        }     

        if (!self::$PageTerm['1'] = self::$modx->getObject('PageTerm', array('term_id'=>self::$Term['a']->get('id'),'page_id'=>self::$P['1']->get('id')))) {
            self::$PageTerm['1'] = self::$modx->newObject('PageTerm');
            self::$PageTerm['1']->fromArray(array(
                'page_id' => self::$P['1']->get('id'),
                'term_id' => self::$Term['a']->get('id'),
                'seq' => 0
            ));
            self::$PageTerm['1']->save();        
        }    

        if (!self::$PageTerm['2'] = self::$modx->getObject('PageTerm', array('term_id'=>self::$Term['b']->get('id'),'page_id'=>self::$P['1']->get('id')))) {
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
     *
     */
    public static function tearDownAfterClass() {



        self::$Tax->remove();
        self::$Term['a']->remove();
        self::$Term['b']->remove();
        self::$Term['c']->remove();

        self::$P['1']->remove();
        self::$P['2']->remove();
        self::$P['3']->remove();

        self::$PageTerm['1']->remove();
        self::$PageTerm['2']->remove();
        


    }
    
    private function _resetTermParent() {
        // Make sure the terms are all put back under the parent tax
        self::$Term['a']->set('parent',self::$Tax->get('id'));
        self::$Term['a']->save();
        self::$Term['b']->set('parent',self::$Tax->get('id'));
        self::$Term['b']->save();
        self::$Term['c']->set('parent',self::$Tax->get('id'));
        self::$Term['c']->save();     
    }
    /**
     * The trickiest thing here is how we track and update the parent taxonomy properties 
     * and the hierarchical terms.
     *
     Taxonomy Properties
    {
        "children":{
            "225":{
                "alias":"test-term-3",
                "pagetitle":"Term 3",
                "published":true,
                "menuindex":0,
                "children":[]
            }
        },
        "children_ids":{
            "225":true
        }
    }
     
     Term Properties (simple, no children):
     {
        "fingerprint":"f391e15428e162fe2c1744f5359490d4",
        "prev_parent":224
    }
    
    Term Properties (hierarchical, with children):     
    {
        "fingerprint":"1e735c4df641479da517f3b3fdd06266",
        "prev_parent":220,
        "children":{
            "223":{
                "alias":"test-term-1",
                "pagetitle":"Term 1",
                "published":true,
                "menuindex":0,"children":[]
            }
        },
        "children_ids":{
            "223":true
        }
    }     
     
     */
/*
    public function testBasicProperties() {
        //self::$modx->setLogTarget('ECHO');
        //self::$modx->setLogLevel(4);    
       
        $this->_resetTermParent();
        
        $p = self::$Tax->get('properties');
        //print_r($p); exit;
        $this->assertTrue(isset($p['children_ids']));
        $this->assertTrue(isset($p['children']) && is_array($p['children']));        
        $this->assertTrue($p['children_ids'][ self::$Term['a']->get('id') ]);
        $this->assertTrue($p['children_ids'][ self::$Term['b']->get('id') ]);
        $this->assertTrue($p['children_ids'][ self::$Term['c']->get('id') ]); 
        
        foreach ($p['children'] as $term_id => $tdata) {
            $T = self::$modx->getObject('Term', $term_id);
            $this->assertTrue(!empty($T));
            $this->assertEquals($T->get('pagetitle'), $tdata['pagetitle']);
            $tprops = $T->get('properties');
            $this->assertTrue(empty($tprops['children'])); // no sub-children
        }    
        
    }
*/

    public function testMoveTerm() {
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
    public function testGetPageTerms() {
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
        $expected = '<ul><li>'.self::$Term['a']->get('id').' : Test Term A</li><li>'.self::$Term['b']->get('id').' : Test Term B</li></ul>';
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test the testGetPageTerms Snippet
     *
     */
    public function testGetPageTermsLimit() {
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
        $expected = '<ul><li>'.self::$Term['a']->get('id').' : Test Term A</li></ul>';
        $this->assertEquals($expected, $actual);
    }

    public function testGetTagCloud() {
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

    public function testGetTagCloudIncludeEmpty() {
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
        $this->assertEquals($expected, $actual);  
    }

    /**
     * Creating a new term should ripple up the hierarchy.
     *
     */
    public function testCreateRippleUp() {
    
    }
    
    public function testSnippet() {
/*
        global $modx;
        $modx = self::$modx;
        
        $props = array();
        $props['options'] = 'sample-api-key';
        $props['name'] = 'price';
        $props['input'] = '29.99';
        $actual = $modx->runSnippet('secure', $props);
*/
        

        
    }

}
