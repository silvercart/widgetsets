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
     * db fields
     * 
     * @var array
     */
    public static $db = array(
        'InheritFromParent' => 'Boolean(1)',
    );

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
     * contains all registered widget sets
     *
     * @var array
     */
    protected $registeredWidgetSets = null;

    /**
     * contains all controller for the registered widget sets
     *
     * @var array
     */
    protected $registeredWidgetSetController = array();

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

        $inheritFromParentField      = new CheckboxField('InheritFromParent', '');
        $inheritFromParentFieldGroup = new FieldGroup($inheritFromParentField);
        $inheritFromParentFieldGroup->setTitle($this->owner->fieldLabel('InheritFromParent'));

        $config = GridFieldConfig_RelationEditor::create();

        $widgetSetSidebarLabel = new HeaderField('WidgetSetSidebarLabel', $this->owner->fieldLabel('WidgetSetSidebarLabel'));
        $widgetSetSidebarField = new GridField("WidgetSetSidebar", $this->owner->fieldLabel('AssignedWidgets'), $this->owner->WidgetSetSidebar(), $config);

        $widgetSetContentlabel = new HeaderField('WidgetSetContentLabel', $this->owner->fieldLabel('WidgetSetContentLabel'));
        $widgetSetContentField = new GridField("WidgetSetContent", $this->owner->fieldLabel('AssignedWidgets'), $this->owner->WidgetSetContent(), $config);

        $fields->addFieldToTab("Root.Widgets", $inheritFromParentFieldGroup);
        $fields->addFieldToTab("Root.Widgets", $widgetSetSidebarLabel);
        $fields->addFieldToTab("Root.Widgets", $widgetSetSidebarField);
        $fields->addFieldToTab("Root.Widgets", $widgetSetContentlabel);
        $fields->addFieldToTab("Root.Widgets", $widgetSetContentField);
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 25.01.2013
     */
    public function updateFieldLabels(&$labels) {
        $labels = array_merge(
                $labels,
                array(
                    'WidgetSetContentLabel' => _t('WidgetSetWidgets.WIDGETSET_CONTENT_FIELD_LABEL'),
                    'WidgetSetSidebarLabel' => _t('WidgetSetWidgets.WIDGETSET_SIDEBAR_FIELD_LABEL'),
                    'AssignedWidgets'       => _t('WidgetSetWidgets.ASSIGNED_WIDGETS'),
                    'InheritFromParent'     => _t('WidgetSetWidgets.INHERIT_FROM_PARENT'),
                )
        );
    }

    /**
     * Returns all registered widget sets as associative array.
     * 
     * @return array
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 30.01.2013
     */
    public function getRegisteredWidgetSets() {
        return $this->registeredWidgetSets;
    }
    
    /**
     * Registers a WidgetSet.
     * 
     * @param string        $widgetSetName  The name of the widget set (used as array key)
     * @param DataObjectSet $widgetSetItems The widget set items (usually coming from a relation)
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.05.2011
     */
    public function registerWidgetSet($widgetSetName, $widgetSetItems) {
        $this->registeredWidgetSets[$widgetSetName] = $widgetSetItems;
    }

    /**
     * Loads the widget controllers into class variables so that we can use
     * them in method 'InsertWidgetArea'.
     * 
     * @param Page $context The owner of this extension, i.e. the extended object
     * 
     * @return void
     *  
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 30.01.2013
     */
    public function loadWidgetControllers($context = null) {
        if (is_null($context)) {
            $context = $this->owner;
        } 

        $registeredWidgetSets = $context->getRegisteredWidgetSets();

        foreach ($registeredWidgetSets as $registeredWidgetSetName => $registeredWidgetSetItems) {
            $controller = new ArrayList();

            foreach ($registeredWidgetSetItems as $registeredWidgetSetItem) {
                $widgets = $registeredWidgetSetItem->WidgetArea()->WidgetControllers();
                $widgets->sort('Sort', 'ASC');
                $controller->merge(
                    $widgets
                );
            }

            $context->registerWidgetSetController($registeredWidgetSetName, $controller);
        }
    }

    /**
     * method to register a widgetset controller for a widgetset name
     *
     * @param  string    $registeredWidgetSetName widgetsetname
     * @param  ArrayList $controller              controller for this widgetset
     *
     * @return void
     */
    public function registerWidgetSetController($registeredWidgetSetName, $controller) {
        $this->registeredWidgetSetController[$registeredWidgetSetName] = $controller;
    }


    /**
     * returns the controller fro the given widgetset name
     *
     * @param  string $registeredWidgetSetName widgetset name
     *
     * @return ArrayList
     */
    public function getRegisteredWidgetSetController($registeredWidgetSetName) {
        $controller = null;
        if (array_key_exists($registeredWidgetSetName, $this->registeredWidgetSetController)) {
            $controller = $this->registeredWidgetSetController[$registeredWidgetSetName];
        }
        return $controller;
    }

    /**
     * this method renders the output for all widgetsets and returns it
     * if the current page has no widgetset for the actual area, has a parent
     * and the option InheritFromParent is set it will recursively render the
     * parent widgetsets
     *
     * @param Page   $pageToLoadFrom Parent page to load the widgets from
     * @param string $identifier     Identifier of the widget set to load    
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 30.01.2013
     */
    public function getWidgetSetsFromParent($pageToLoadFrom, $identifier) {
        $output = '';
        $controllerName = 'WidgetSet'.$identifier;

        $pageToLoadFrom->registerWidgetSet($controllerName, $pageToLoadFrom->{$controllerName}());
        $pageToLoadFrom->loadWidgetControllers();
        $controller = $pageToLoadFrom->getRegisteredWidgetSetController($controllerName);

        if ((is_null($controller) ||
            $controller->Count() == 0) &&
            $pageToLoadFrom->ParentID > 0 &&
            $pageToLoadFrom->InheritFromParent) {

            return $this->getWidgetSetsFromParent($pageToLoadFrom->Parent(), $identifier);
        } elseif (!is_null($controller) &&
                  $controller->Count() > 0) {

            foreach ($controller as $widgetSet) {
                $output .= $widgetSet->WidgetHolder();
            }
        }
        return $output;
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
class WidgetSetPageExtension_Controller extends DataExtension {

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
