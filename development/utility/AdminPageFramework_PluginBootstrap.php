<?php
/**
 * Provides an abstract base to create a bootstrap class for Wordpress plugins.
 *    
 * @package      Admin Page Framework Loader
 * @copyright    Copyright (c) 2015, <Michael Uno>
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 * @since        3.5.0
 * 
 */

/**
 * Loads the plugin.
 * 
 * @action      do      {hook prefix}_action_before_loading_plugin
 * @action      do      {hook prefix}_action_after_loading_plugin
 * @since       3.5.0
 */
abstract class AdminPageFramework_PluginBootstrap {
    
    /**
     * Stores the caller file path.
     */
    public $sFilePath;
    
    /**
     * Stores whether the script is loaded in the admin area.
     */
    public $bIsAdmin;
    
    /**
     * Stores the hook prefix.
     */
    public $sHookPrefix;
    
    /**
     * Indicates whether the bootstrap has been loaded or not so that multiple instances of this class won't be created.      
     * @internal
     */
    static public $_bLoaded = false;
        
    /**
     * Sets up properties and hooks.
     * 
     */
    public function __construct( $sPluginFilePath, $sPluginHookPrefix='' ) {
        
        // Do not allow multiple instances per page load.
        if ( self::$_bLoaded ) { return; }
        self::$_bLoaded = true;
        
        // Set up properties
        $this->sFilePath   = $sPluginFilePath;
        $this->bIsAdmin    = is_admin();
        $this->sHookPrefix = $sPluginHookPrefix;
        
        // 1. Define constants.
        $this->setConstants();
        
        // 2. Set global variables.
        $this->setGlobals();
            
        // 3. Set up auto-load classes.
        $this->_registerClasses();

        // 4. Set up activation hook.
        register_activation_hook( $this->sFilePath, array( $this, 'replyToPluginActivation' ) );
        
        // 5. Set up deactivation hook.
        register_deactivation_hook( $this->sFilePath, array( $this, 'replyToPluginDeactivation' ) );
                 
        // 6. Schedule to load plugin specific components.
        add_action( 'plugins_loaded', array( $this, '_replyToLoadPluginComponents' ) );
        
        // 7. Set up localization
        add_action( 'init', array( $this, 'setLocalization' ) );
        
        // 8. Call user constructors.
        $this->construct();
        $this->start();
        
    }    
    
        /**
         * Register classes to be auto-loaded.
         * 
         * @since       3.5.0
         */
        protected function _registerClasses() {
            
            if ( ! class_exists( 'AdminPageFramework_RegisterClasses' ) ) {
                return;
            }                        
            // Register classes
            new AdminPageFramework_RegisterClasses( 
                $this->getScanningDirs(),   // scanning directory paths
                array(),                    // autoloader options
                $this->getClasses()         // pre-generated class list
            );
                    
        }

        /**
         * Loads the plugin specific components. 
         * 
         * @remark      All the necessary classes should have been already loaded.
         * @since       3.5.0
         */
        public function _replyToLoadPluginComponents() {

            if ( $this->sHookPrefix ) {
                do_action( "{$this->sHookPrefix}_action_before_loading_plugin" );
            }
        
            $this->setUp();
            
            // Modules should use this hook.
            if ( $this->sHookPrefix ) {
                do_action( "{$this->sHookPrefix}_action_after_loading_plugin" );
            }
            
        }        
        
    /*
     * Shared Methods. Users override these methods in the extended class.
     */
    
    /**
     * Sets up constants.
     */
    public function setConstants() {}
    
    /**
     * Sets up global variables.
     */
    public function setGlobals() {}
    
    /**
     * Returns an array holding class names in the key and the file path to the value.
     * The returned array will be passed to the autoloader class.
     * @since       3.5.0
     * @return      array       An array holding PHP classes.
     */
    public function getClasses() {
        
        $_aClasses = array();
        
        // Example
        // include( dirname( $this->sFilePath ) . '/include/admin-page-framework-loader-include-class-file-list.php' );
        
        return $_aClasses;
    }
    
    /**
     * Returns an array holding scanning directory paths.
     * @since       3.5.0
     * @return      array       An array holding directory paths.
     */
    public function getScanningDirs() {
        $_aDirs = array();
        return $_aDirs;
    }
    
    /**
     * The plugin activation callback method.
     */    
    public function replyToPluginActivation() {}

    /**
     * The plugin deactivation callback method.
     * 
     * @since       3.5.0
     */
    public function replyToPluginDeactivation() {}
        
    /**
     * Load localization files.
     *
     * @since       3.5.0
     */
    public function setLocalization() {}        
    
    /**
     * Loads plugin components.
     * 
     * Use this method to load the main plugin components such as post type, admin pages, event routines etc 
     * as this method is triggered with the 'plugins_loaded' hook which is triggered after all the plugins are loaded.
     * 
     * On the other hand, for extension plugins, use the construct() method below and hook into the "{$this->sHookPrefix}_action_after_loading_plugin" action hook.
     * This way, extension plugin can load their components after the main plugin components get loaded.
     * 
     * @since       3.5.0
     */
    public function setUp() {}
        
    /**
     * The protected user constructor method.
     * 
     * For extension plugins, use this method to hook into the "{$this->sHookPrefix}_action_after_loading_plugin" action hook.
     * 
     * @since       3.5.0
     * @access      protected       This is meant to be called within the class definition. For public access use the `start()` method.
     */
    protected function construct() {}
 
    /**
     * The public user constructor method.
     * 
     * @since       3.5.0
     * @access      public
     */
    public function start() {}
 
}