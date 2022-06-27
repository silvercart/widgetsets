<?php

namespace WidgetSets\Extensions;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Widgets\Model\Widget;
use WidgetSets\Forms\OptionsetField;
use WidgetSets\Model\WidgetSetWidget;

/**
 * Extension for a {@link Widget}
 *
 * @package WidgetSets
 * @subpackage Extensions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.08.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class WidgetSetWidgetExtension extends DataExtension
{
    /**
     * array which holds all classnames of widgets which should
     * not be able to create in a widgetset
     * 
     * @var array
     */
    private static $hidden_widgets = [
        Widget::class,
        WidgetSetWidget::class,
    ];

    /**
     * Extend permissions to include additional security for objects that are not published to live.
     *
     * @param Member $member 
     * 
     * @return bool|null
     */
    public function canView($member = null)
    {
        return $this->owner->canViewVersioned($member);
    }

    /**
     * manipulates the cms fields
     *
     * @param FieldList $fields cms fields
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2017
     */
    public function updateCMSFields(FieldList $fields)
    {
        if (!$this->owner->isInDB()) {
            $descendants = ClassInfo::subclassesFor(Widget::class);
            foreach (self::$hidden_widgets as $className) {
                $key = strtolower($className);
                if (array_key_exists($key, $descendants)) {
                    unset($descendants[$key]);
                }
            }
            foreach ($descendants as $descendant => $className) {
                unset($descendants[$descendant]);
                $defaultTitle                        = singleton($className)->i18n_singular_name();
                $descendants[$className]             = _t("{$className}.TITLE", $defaultTitle);
                $descendantsDescriptions[$className] = _t("{$className}.DESCRIPTION", $defaultTitle);
            }
            asort($descendants);
            $fields->push(OptionsetField::create('ClassName', _t(WidgetSetWidget::class . '.Type', 'Type'), $descendants)->setOptionDescriptions($descendantsDescriptions));
            
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
    public static function prevent_widget_creation_by_class($widgetClass)
    {
        if (!is_null($widgetClass)
            && !in_array($widgetClass, self::$hidden_widgets)
        ) {
            self::$hidden_widgets[] = $widgetClass;
        }
    }

}