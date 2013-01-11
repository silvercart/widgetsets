<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of the Widgetsets module.
 *
 * Widgetsets module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * It is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this package. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Widgetsets
 * @subpackage Base
 */

/**
 * Provides some basic functionality for all Widgetset widgets.
 *
 * @package Widgetsets
 * @subpackage Base
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 04.01.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2013 pixeltricks GmbH
 */
class WidgetSetWidget extends Widget {

    /**
     * Attributes
     *
     * @var array
     */
    public static $db = array(
        'ExtraCssClasses'   => 'VarChar(255)',
    );
}

/**
 * Provides some basic functionality for all Widgetset widgets.
 *
 * @package Widgetsets
 * @subpackage Base
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 04.01.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2013 pixeltricks GmbH
 */
class WidgetSetWidget_Controller extends Widget_Controller {

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
}
