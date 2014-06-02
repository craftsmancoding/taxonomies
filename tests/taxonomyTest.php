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
                'parent' => self::$Tax->get('id'),
                'pagetitle' => 'Test Term A',
                'alias' => 'test-term-a',
                'class_key' => 'Term',
                'isfolder' => 1,
                'published' => 1
            ));
            self::$Term['a']->save();        
        }
        if (!self::$Term['b'] = self::$modx->getObject('Term', array('alias'=>'test-term-b'))) {
            self::$Term['b'] = self::$modx->newObject('Term');
            self::$Term['b']->fromArray(array(
                'parent' => self::$Tax->get('id'),
                'pagetitle' => 'Test Term B',
                'alias' => 'test-term-b',
                'class_key' => 'Term',
                'isfolder' => 1,
                'published' => 1
            ));
            self::$Term['b']->save();        
        }
        if (!self::$Term['c'] = self::$modx->getObject('Term', array('alias'=>'test-term-c'))) {
            self::$Term['c'] = self::$modx->newObject('Term');
            self::$Term['c']->fromArray(array(
                'parent' => self::$Tax->get('id'),
                'pagetitle' => 'Test Term C',
                'alias' => 'test-term-c',
                'class_key' => 'Term',
                'isfolder' => 1,
                'published' => 1
            ));
            self::$Term['c']->save();        
        }
        
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
        
    }
    
    /**
     *
     */
    public static function tearDownAfterClass() {

/*
        self::$Store->remove();
        self::$Tax->remove();
        self::$Term['a']->remove();
        self::$Term['b']->remove();
        self::$Term['c']->remove();

        self::$P['1']->remove();
        self::$P['2']->remove();
        self::$P['3']->remove();
        
*/

    }
    
    
    /**
     * 
     *
     */
    public function testRelations() {
        //self::$modx->setLogTarget('ECHO');
        //self::$modx->setLogLevel(4);    

        
    }

}
