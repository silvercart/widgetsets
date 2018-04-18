<?php

namespace WidgetSets\Model;

use SilverStripe\Widgets\Model\Widget;

/**
 * Provides some basic functionality for all Widgetset widgets.
 *
 * @package WidgetSets
 * @subpackage Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class WidgetSetWidget extends Widget {

    /**
     * Attributes
     *
     * @var array
     */
    private static $db = array(
        'ExtraCssClasses' => 'Varchar(255)',
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'WidgetSetWidget';
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.03.2014
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'ExtraCssClasses'       => _t(WidgetSetWidget::class . '.CssFieldLabel', 'Additional CSS class (optional):'),
                    'FrontTitle'            => _t(WidgetSetWidget::class . '.FRONTTITLE', 'Headline'),
                    'FrontContent'          => _t(WidgetSetWidget::class . '.FRONTCONTENT', 'Content'),
                    'WidgetSetContentLabel' => _t(WidgetSetWidget::class . '.WIDGETSET_CONTENT_FIELD_LABEL', 'Widgets for the content area'),
                    'WidgetSetSidebarLabel' => _t(WidgetSetWidget::class . '.WIDGETSET_SIDEBAR_FIELD_LABEL', 'Widgets for the sidebar'),
                    'AssignedWidgets'       => _t(WidgetSetWidget::class . '.ASSIGNED_WIDGETS', 'Assigned Widgets'),
                    'InheritFromParent'     => _t(WidgetSetWidget::class . '.INHERIT_FROM_PARENT', 'Inherit widgets from parent'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}