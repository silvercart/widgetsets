<?php

namespace WidgetSets\Extensions;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\SS_List;
use WidgetSets\Model\WidgetSet;
use WidgetSets\Model\WidgetSetWidget;

/**
 * SiteTree extension to add {@link WidgetSet} to a page.
 *
 * @package WidgetSets
 * @subpackage Extensions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class WidgetSetSiteTreeExtension extends DataExtension
{
    /**
     * db fields
     * 
     * @var array
     */
    private static $db = [
        'InheritFromParent' => 'Boolean(1)',
    ];
    /**
     * array to add many_many relations
     *
     * @var array
     */
    private static $many_many = [
        'WidgetSetSidebar' => WidgetSet::class,
        'WidgetSetContent' => WidgetSet::class,
    ];
    /**
     * contains all registered widget sets
     *
     * @var array
     */
    protected $registeredWidgetSets = [];
    /**
     * contains all controller for the registered widget sets
     *
     * @var array
     */
    protected $registeredWidgetSetController = [];

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
    public function updateCMSFields(FieldList $fields) : void
    {
        $inheritFromParentField      = CheckboxField::create('InheritFromParent', '');
        $inheritFromParentFieldGroup = FieldGroup::create($inheritFromParentField);
        $inheritFromParentFieldGroup->setTitle($this->owner->fieldLabel('InheritFromParent'));
        $config                      = GridFieldConfig_RelationEditor::create();
        $widgetSetSidebarLabel       = HeaderField::create('WidgetSetSidebarLabel', $this->owner->fieldLabel('WidgetSetSidebarLabel'));
        $widgetSetSidebarField       = GridField::create("WidgetSetSidebar", $this->owner->fieldLabel('AssignedWidgets'), $this->owner->WidgetSetSidebar(), $config);
        $widgetSetContentlabel       = HeaderField::create('WidgetSetContentLabel', $this->owner->fieldLabel('WidgetSetContentLabel'));
        $widgetSetContentField       = GridField::create("WidgetSetContent", $this->owner->fieldLabel('AssignedWidgets'), $this->owner->WidgetSetContent(), $config);
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
     * @return void
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 25.01.2013
     */
    public function updateFieldLabels(&$labels) : void
    {
        $labels = array_merge($labels, [
            'WidgetSetContentLabel' => WidgetSetWidget::singleton()->fieldLabel('WidgetSetContentLabel'),
            'WidgetSetSidebarLabel' => WidgetSetWidget::singleton()->fieldLabel('WidgetSetSidebarLabel'),
            'AssignedWidgets'       => WidgetSetWidget::singleton()->fieldLabel('AssignedWidgets'),
            'InheritFromParent'     => WidgetSetWidget::singleton()->fieldLabel('InheritFromParent'),
        ]);
    }

    /**
     * Returns all registered widget sets as associative array.
     * 
     * @return array
     */
    public function getRegisteredWidgetSets() : array
    {
        return $this->registeredWidgetSets;
    }
    
    /**
     * Registers a WidgetSet.
     * 
     * @param string  $widgetSetName  The name of the widget set (used as array key)
     * @param SS_List $widgetSetItems The widget set items (usually coming from a relation)
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.05.2011
     */
    public function registerWidgetSet(string $widgetSetName, $widgetSetItems) : void
    {
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
    public function loadWidgetControllers($context = null) : void
    {
        if (is_null($context)) {
            $context = $this->owner;
        } 
        $registeredWidgetSets = $context->getRegisteredWidgetSets();
        foreach ($registeredWidgetSets as $registeredWidgetSetName => $registeredWidgetSetItems) {
            $controller = ArrayList::create();
            foreach ($registeredWidgetSetItems as $registeredWidgetSetItem) {
                $widgets = $registeredWidgetSetItem->WidgetArea()->WidgetControllers();
                $widgets->sort('Sort', 'ASC');
                $controller->merge($widgets);
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
    public function registerWidgetSetController(string $registeredWidgetSetName, $controller) : void
    {
        $this->registeredWidgetSetController[$registeredWidgetSetName] = $controller;
    }


    /**
     * returns the controller fro the given widgetset name
     *
     * @param  string $registeredWidgetSetName widgetset name
     *
     * @return ArrayList
     */
    public function getRegisteredWidgetSetController(string $registeredWidgetSetName)
    {
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
     * @return string
     */
    public function getWidgetSetsFromParent($pageToLoadFrom, string $identifier) : DBHTMLText
    {
        $output         = '';
        $controllerName = "WidgetSet{$identifier}";
        $pageToLoadFrom->registerWidgetSet($controllerName, $pageToLoadFrom->{$controllerName}());
        $pageToLoadFrom->loadWidgetControllers();
        $controller     = $pageToLoadFrom->getRegisteredWidgetSetController($controllerName);
        if ((is_null($controller)
          || $controller->Count() == 0)
         && $pageToLoadFrom->ParentID > 0
         && $pageToLoadFrom->InheritFromParent
        ) {
            return $this->getWidgetSetsFromParent($pageToLoadFrom->Parent(), $identifier);
        } elseif (!is_null($controller)
               && $controller->Count() > 0
        ) {
            $widgetAreaID = 0;
            foreach ($controller as $widget) {
                if (!$widget->canView()) {
                    continue;
                }
                if ($widgetAreaID != $widget->ParentID) {
                    $widgetAreaID = $widget->ParentID;
                }
                $output .= $widget->WidgetHolder();
            }
        }
        return DBHTMLText::create()->setValue($output);
    }
    
    /**
     * Returns whether the widget set with the given name has widgets.
     * 
     * @param string $registeredWidgetSetName Name of the widget set to check
     * 
     * @return bool
     */
    public function hasWidgets(string $registeredWidgetSetName) : bool
    {
        $has = false;
        foreach ($this->owner->{$registeredWidgetSetName}() as $widgetSet) {
            /* @var $widgetSet WidgetSet */
            if ($widgetSet->WidgetArea()->Widgets()->count() > 0) {
                $has = true;
                break;
            }
        }
        return $has;
    }
}