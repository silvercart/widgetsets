<?php

namespace WidgetSets\Model;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\ORM\DataObject;
use SilverStripe\Widgets\Model\WidgetArea;
use WidgetSets\Dev\WidgetSetTools;
use WidgetSets\Model\WidgetSetWidget;

/**
 * Contains an arbitrary number of widgets.
 *
 * @package WidgetSets
 * @subpackage Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class WidgetSet extends DataObject
{
    /**
     * Attributes
     *
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(255)'
    ];
    /**
     * Has-one relationships
     *
     * @var array
     */
    private static $has_one = [
        'WidgetArea' => WidgetArea::class,
    ];
    /**
     * Has-many relationships
     *
     * @var array
     */
    private static $belongs_many_many = [
        'Pages' => SiteTree::class,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'WidgetSet';

    /**
     * Returns the translated singular name of the given object. If no
     * translation exists the class name will be returned.
     *
     * @return string The objects singular name
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.01.2013
     */
    public function singular_name()
    {
        return WidgetSetTools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     *
     * @return string the objects plural name
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.01.2013
     */
    public function plural_name()
    {
        return WidgetSetTools::plural_name_for($this);
    }

    /**
     * Returns the GUI fields for the storeadmin.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        $result = $this->extend('overrideGetCMSFields');

        if (is_array($result)
            && array_key_exists(0, $result)
            && $result[0] instanceof FieldList
        ) {
            $fields = $result[0];
        } else {
            $fields = parent::getCMSFields();

            if ($this->isInDB()) {
                $fields->addFieldsToTab('Root.Main', $this->scaffoldWidgetAreaFields());
            }
            $fields->removeByName('WidgetAreaID');
        }

        return $fields;
    }

    /**
     * Scaffolds the relation WidgetArea into the WidgetSet CMSFields and configurates
     * the GridField
     * 
     * @return FieldList
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Patrick Schneider <pschneider@pixeltricks.de>
     * @since 05.03.2014
     */
    public function scaffoldWidgetAreaFields()
    {
        return self::scaffold_widget_area_fields_for($this);
    }
    
    /**
     * Scaffolds the relation WidgetArea into the context CMSFields and configurates
     * the GridField.
     * 
     * @param DataObject $context Context to get Widget admin for
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.03.2014
     */
    public static function scaffold_widget_area_fields_for($context)
    {
        $fields = $context->WidgetArea()->scaffoldFormFields(
                [
                    'includeRelations' => ($context->isInDB()),
                    'tabbed'           => false,
                    'ajaxSafe'         => true,
                ]
        );
        if ($context->isInDB()) {
            $widgetsField = $fields->dataFieldByName('Widgets');
            $widgetsFieldConfig = $widgetsField->getConfig();
            $widgetsFieldConfig->removeComponentsByType('GridFieldAddExistingAutocompleter');
            if (class_exists('\Symbiote\GridFieldExtensions\GridFieldOrderableRows')) {
                $widgetsFieldConfig->addComponent(new \Symbiote\GridFieldExtensions\GridFieldOrderableRows('Sort'));
            } elseif (class_exists('\UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows')) {
                $widgetsFieldConfig->addComponent(new \UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows('Sort'));
            }
            $widgetsFieldConfig->getComponentByType(GridFieldDataColumns::class)->setDisplayFields(
                [
                    'Title'    => $context->fieldLabel('Title'),
                    'CMSTitle' => _t(WidgetSetWidget::class . '.Type', 'Type'),
                ]
            );
            // this is configured with a remove relation button by default which results in unaccessible widgets
            $widgetsFieldConfig->removeComponentsByType(GridFieldDeleteAction::class);
            // so we add a new one without a relation button
            $widgetsFieldConfig->addComponent(new GridFieldDeleteAction());
        }
        $fields->removeByName('LinkTracking');
        $fields->removeByName('FileTracking');
        
        return $fields;
    }

    /**
     * Summary fields for display in tables.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 04.01.2013
     */
    public function summaryFields()
    {
        $fields = [
            'Title' => $this->fieldLabel('Title')
        ];

        return $fields;
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2013
     */
    public function fieldLabels($includerelations = true)
    {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                [
                    'Title' => _t(WidgetSet::class . '.TITLE', 'Title'),
                    'Pages' => _t(WidgetSet::class . '.PAGES', 'assigned pages'),
                ]
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * We have to create a WidgetArea object if there's none attributed yet.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.08.2018
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if ($this->WidgetAreaID == 0) {
            $widgetArea = WidgetArea::create();
            $widgetArea->write();

            $this->WidgetAreaID = $widgetArea->ID;
            $this->write();
        }
    }

    /**
     * We want to delete all attributed WidgetAreas and Widgets before deletion.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 04.01.2013
     */
    public function onBeforeDelete()
    {
        parent::onBeforeDelete();

        foreach ($this->WidgetArea()->Widgets() as $widget) {
            $widget->delete();
        }

        $this->WidgetArea()->delete();
    }
}
