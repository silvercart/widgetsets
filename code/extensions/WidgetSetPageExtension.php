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
 * @subpackage Extensions
 */

/**
 * Page extension to add {@link WidgetSet} to a page
 *
 * @package Widgetsets
 * @subpackage Extensions
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 04.01.2013
 * @copyright 2013 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class WidgetSetPageExtension extends DataExtension {

    /**
     * array to add many_many relations
     *
     * @var array
     */
    public static $many_many = array(
        'WidgetSetSidebar' => 'WidgetSet',
        'WidgetSetContent' => 'WidgetSet',
    );

    /**
     * updates cms fields and adds widgetset gridfields
     *
     * @param FieldList $fields fields
     *
     * @return void
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 04.01.2013
     */
    public function updateCMSFields(FieldList $fields) {
        // create the configuration for the grid fields
        $config = GridFieldConfig_RelationEditor::create();

        // ...and create them
        $widgetSetSidebarLabel = new HeaderField('WidgetSetSidebarLabel', _t('WidgetSetWidgets.WIDGETSET_SIDEBAR_FIELD_LABEL'));
        $widgetSetSidebarField = new GridField("WidgetSetSidebar", "Sidebar widgets", $this->owner->WidgetSetSidebar(), $config);

        $widgetSetContentlabel = new HeaderField('WidgetSetContentLabel', _t('WidgetSetWidgets.WIDGETSET_CONTENT_FIELD_LABEL', 'Content'));
        $widgetSetContentField = new GridField("WidgetSetContent", "Sidebar widgets", $this->owner->WidgetSetContent(), $config);

        $fields->addFieldToTab("Root.Widgets", $widgetSetSidebarLabel);
        $fields->addFieldToTab("Root.Widgets", $widgetSetSidebarField);
        $fields->addFieldToTab("Root.Widgets", $widgetSetContentlabel);
        $fields->addFieldToTab("Root.Widgets", $widgetSetContentField);
    }

}

/**
 * Page_Controller extension
 *
 * @package Widgetsets
 * @subpackage Extensions
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 04.01.2013
 * @copyright 2013 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class WidgetSetPageExtenstion_Controller extends DataExtension {

    /**
     * Contains the output of all WidgetSets of the parent page
     *
     * @var array
     */
    protected $widgetOutput = array();

    /**
     * Contains the controllers for the sidebar widgets
     *
     * @var DataObjectSet
     */
    protected $WidgetSetSidebarControllers;

    /**
     * Contains the controllers for the content area widget
     *
     * @var DataObjectSet
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
        $controller = Controller::curr();
        if ($controller == $this->owner || $controller->forceLoadOfWidgets) {
            $this->loadWidgetControllers();
        }
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 04.01.2013
     */
    public function InsertWidgetArea($identifier = 'Sidebar') {
        $output         = '';
        $controllerName = 'WidgetSet'.$identifier.'Controllers';

        if (!isset($this->$controllerName)) {
            return $output;
        }

        foreach ($this->$controllerName as $controller) {
            $output .= $controller->WidgetHolder();
        }

        if (empty($output)) {
            if (isset($this->widgetOutput[$identifier])) {
                $output = $this->widgetOutput[$identifier];
            }
        }

        return $output;
    }

    /**
     * Adds a widget output to the class variable "$this->widgetOutput".
     *
     * @param string $key    The key for the output
     * @param string $output The actual output of the widget
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 04.01.2013
     */
    public function saveWidgetOutput($key, $output) {
        $this->widgetOutput[$key] = $output;
    }

    /**
     * Loads the widget controllers into class variables so that we can use
     * them in method 'InsertWidgetArea'.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 04.01.2013
     */
    protected function loadWidgetControllers() {
        // Sidebar area widgets -----------------------------------------------
        $controllers = new ArrayList();

        foreach ($this->owner->WidgetSetSidebar() as $widgetSet) {
            $controllers->merge(
                $widgetSet->WidgetArea()->WidgetControllers()
            );
        }

        $this->WidgetSetSidebarControllers = $controllers;
        $this->WidgetSetSidebarControllers->sort('Sort', 'ASC');

        // Content area widgets -----------------------------------------------
        $controllers = new ArrayList();

        foreach ($this->owner->WidgetSetContent() as $widgetSet) {
            $controllers->merge(
                $widgetSet->WidgetArea()->WidgetControllers()
            );
        }
        $this->WidgetSetContentControllers = $controllers;
        $this->WidgetSetContentControllers->sort('Sort', 'ASC');
    }
}