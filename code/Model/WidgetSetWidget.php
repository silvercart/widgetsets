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
        'ExtraCssClasses' => 'VarChar(255)',
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
                    'ExtraCssClasses' => _t('WidgetSets\Model\WidgetSetWidget.CssFieldLabel')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}