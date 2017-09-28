<?php

namespace WidgetSets\Extensions;

use SilverStripe\Core\Manifest\ClassManifest;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use WidgetSets\Model\WidgetSetWidget;

/**
 * Extension for a {@link Widget}
 *
 * @package WidgetSets
 * @subpackage Extensions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class WidgetSetWidgetExtension extends DataExtension {

    /**
     * array which holds all classnames of widgets which should
     * not be able to create in a widgetset
     * 
     * @var array
     */
    private static $hidden_widgets = array(
        WidgetSetWidget::class,
    );

    /**
     * manipulates the cms fields
     *
     * @param FieldList $fields cms fields
     *
     * @return void
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 04.01.2013
     */
    public function updateCMSFields(FieldList $fields) {
        if (!$this->owner->isInDB()) {
            $manifest    = new ClassManifest(BASE_PATH);
            $descendants = array_flip($manifest->getDescendantsOf('Widget'));
            
            foreach (self::$hidden_widgets as $className) {
                unset($descendants[$className]);
            }

            foreach ($descendants as $descendant => $index) {
                $descendants[$descendant] = _t($descendant . '.TITLE', $descendant);
            }
            $fields->push(new DropdownField('ClassName', _t('WidgetSets\Model\WidgetSetWidget.Type'), $descendants));
        }
    }

    /**
     * register a widget class which should not be added to a widget set
     * (for example WidgetSetWidget which is only a base class but has no specific functionality to display)
     * 
     * @param string $widgetClass with the classname
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.09.2017
     */
    public static function prevent_widget_creation_by_class($widgetClass) {
        if (!is_null($widgetClass) &&
            !in_array($widgetClass, self::$hidden_widgets)) {
            self::$hidden_widgets[] = $widgetClass;
        }
    }

}