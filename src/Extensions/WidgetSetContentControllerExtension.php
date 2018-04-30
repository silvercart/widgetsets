<?php

namespace WidgetSets\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\SS_List;

/**
 * ContentController extension to add {@link WidgetSet} controller functions.
 *
 * @package WidgetSets
 * @subpackage Extensions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class WidgetSetContentControllerExtension extends Extension {

    /**
     * Contains the output of all WidgetSets of the parent page
     *
     * @var array
     */
    protected $widgetOutput = array();

    /**
     * Contains the controllers for the sidebar widgets
     *
     * @var SS_List
     */
    protected $WidgetSetSidebarControllers;

    /**
     * Contains the controllers for the content area widget
     *
     * @var SS_List
     */
    protected $WidgetSetContentControllers;

    /**
     * load widget controller
     *
     * @return void
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 04.01.2013
     */
    public function onBeforeInit() {
        $this->owner->registerWidgetSet("WidgetSetSidebar", $this->owner->WidgetSetSidebar());
        $this->owner->registerWidgetSet("WidgetSetContent", $this->owner->WidgetSetContent());
    }

    /**
     * Adds a widget output to the class variable "$this->widgetOutput".
     *
     * @param string $key    The key for the output
     * @param string $output The actual output of the widget
     *
     * @return void
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 30.01.2013
     */
    public function saveWidgetOutput($key, $output) {
        $this->widgetOutput[$key] = $output;
    }

    /**
     * returns the rendered widgetOutput for the given widget key
     *
     * @param  string $key widget key
     *
     * @return string
     */
    public function getWidgetOutput($key) {
        $widgetOutput = false;
        if (array_key_exists($key, $this->widgetOutput)) {
            $widgetOutput = $this->widgetOutput[$key];
        }
        return $widgetOutput;
    }

    /**
     * Returns the HTML Code as string for all widgets in the given WidgetArea.
     *
     * If there's no WidgetArea for this page defined we try to get the
     * definition from its parent page.
     * 
     * @param string $identifier The identifier of the widget area to insert
     * 
     * @return string
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 30.01.2013
     */
    public function InsertWidgetArea($identifier = 'Sidebar') {
        $output = $this->getWidgetOutput($identifier);
        if (!$output) {
            $output = $this->owner->getWidgetSetsFromParent($this->owner->dataRecord, $identifier);
            $this->saveWidgetOutput($identifier, $output);
        }
        return $output;
    }

}