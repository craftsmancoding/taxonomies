<?php
/**
 * The name of the controller is based on the action (home) and the
 * namespace. This home controller is loaded by default because of
 * our IndexManagerController.
 */

require_once dirname(dirname(__FILE__)) .'/vendor/autoload.php';
class IndexManagerController extends \Taxonomies\BaseController {

     /**
     * This acts as a class loader.  Beware the difficulties with testing with the "new" keyword!!!
     * See composer.json's autoload section: Controller classes should be found in the controllers/ directory
     * We ignore the incoming $className here and instead fallback to our own mapping which follows the 
     * pattern : \taxonomies\{$Controller_Class_Slug}Controller
     * We can't override the Base controller constructor because this loops back onto it.
     *
     * @param object \modX instance
     * @param string $className (ignored, instead we look to $_REQUEST['class'])
     * @param array array config
     * @return instance of a controller object
     */
    public static function getInstanceDeprecated(\modX &$modx, $className, array $config = array()) {

        $config['method'] = (isset($_REQUEST['method'])) ? $_REQUEST['method'] : 'index';
        $class = (isset($_REQUEST['class'])) ? $_REQUEST['class'] : 'Page'; // Default Controller
        
        if (!is_scalar($class)) {
            throw new \Exception('Invalid data type for class');
        }

        $config['controller_url'] = self::url();
        $config['core_path'] = $modx->getOption('taxonomies.core_path', null, MODX_CORE_PATH.'components/taxonomies/');
        $config['assets_url'] = $modx->getOption('taxonomies.assets_url', null, MODX_ASSETS_URL.'components/taxonomies/');
        
        // If you don't do this, the $_POST array will seem to be populated even during normal GET requests.
        unset($_POST['HTTP_MODAUTH']);
        // Function names are not case sensitive
        if ($_FILES || !empty($_POST)) {
            unset($_POST['_taxonomies']);
            $config['method'] = 'post'.ucfirst($config['method']);
        }
        else {
            $config['method'] = 'get'.ucfirst($config['method']);
        }
        // Classnames are not case-sensitive, but since it triggers the autoloader,
        // we need to manipulate it because some environments are case-sensitive.
        $class = '\\Taxonomies\\'.ucfirst(strtolower($class)).'Controller';
       
        // Override on error
        if (!class_exists($class)) {
            $modx->log(\modX::LOG_LEVEL_ERROR,'[taxonomies] class not found: '.$class,'',__FUNCTION__,__FILE__,__LINE__);            
            $class = '\\Taxonomies\\ErrorController';
            $config['method'] = 'get404';
        }

        $modx->log(\modX::LOG_LEVEL_INFO,'[taxonomies] Instantiating '.$class.' with config '.print_r($config,true),'',__FUNCTION__,__FILE__,__LINE__);
        
        // See Base::render() for how requests get handled.  
        return new $class($modx,$config);

    }

}
/*EOF*/