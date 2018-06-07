<?php

namespace WidgetSets\Controllers;

use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Widgets\Model\WidgetController;

/**
 * WidgetSetWidget Controller class.
 *
 * @package WidgetSets
 * @subpackage Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class WidgetSetWidgetController extends WidgetController {

    /**
     * Instances of $this will have a unique ID
     *
     * @var array
     */
    public static $classInstanceCounter = array();

    /**
     * Contains the unique ID of the current class instance
     *
     * @var int
     */
    protected $classInstanceIdx = 0;

    /**
     * Contains a list of all registered filter plugins.
     *
     * @var array
     */
    public static $registeredFilterPlugins = array();

    /**
     * We register the search form on the page controller here.
     *
     * @param string $widget Not documented in parent class unfortunately
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 04.01.2013
     */
    public function __construct($widget = null) {
        parent::__construct($widget);

        // Initialize or increment the Counter for the form class
        if (!isset(self::$classInstanceCounter[$this->class])) {
            self::$classInstanceCounter[$this->class] = 0;
        } else {
            self::$classInstanceCounter[$this->class]++;
        }

        $this->classInstanceIdx = self::$classInstanceCounter[$this->class];
    }

    /**
     * Registers an object as a filter plugin. Before getting the result set
     * the method 'filter' is called on the plugin. It has to return an array
     * with filters to deploy on the query.
     *
     * @param Object $plugin The filter plugin object
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.01.2013
     */
    public static function registerFilterPlugin($plugin) {
        $reflectionClass = new ReflectionClass($plugin);

        if ($reflectionClass->hasMethod('filter')) {
            self::$registeredFilterPlugins[] = new $plugin();
        }
    }

    /**
     * Overwrites the default widget link to add a proper handling for widget
     * set URLs.
     * 
     * @param string $action Action
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.06.2018
     */
    public function Link($action = null) {
        $id      = ($this->widget) ? $this->widget->ID : null;
        $segment = Controller::join_links('widgetset', $id, $action);
        $page    = Director::get_current_page();
        if ($page &&
            !($page instanceof WidgetController)) {
            return $page->Link($segment);
        }

        $controller = $this->getParentController();
        if ($controller instanceof Controller) {
            return $controller->Link($segment);
        }

        return $segment;
    }
}