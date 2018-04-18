<?php

namespace WidgetSets\Dev;

use ReflectionClass;
use SilverStripe\ORM\DataObject;

/**
 * Provides methods for common tasks.
 *
 * @package WidgetSets
 * @subpackage Dev
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class WidgetSetTools {

    /**
     * Returns the translated singular name of the given object. If no
     * translation exists the class name will be returned.
     *
     * @param DataObject $dataObject DataObject to get singular name for
     *
     * @return string The objects singular name
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.01.2013
     */
    public static function singular_name_for($dataObject) {
        $reflection = new ReflectionClass($dataObject->ClassName);
        $default    = ucwords(trim(strtolower(preg_replace('/_?([A-Z])/', ' $1', $reflection->getShortName()))));
        return _t($dataObject->ClassName . '.SINGULARNAME', $default);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     *
     * @param DataObject $dataObject DataObject to get plural name for
     *
     * @return string the objects plural name
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.01.2013
     */
    public static function plural_name_for($dataObject) {
        $plural_name = self::singular_name_for($dataObject);
        if (substr($plural_name,-1) == 'e') {
            $plural_name = substr($plural_name,0,-1);
        } elseif (substr($plural_name,-1) == 'y') {
            $plural_name = substr($plural_name,0,-1) . 'ie';
        }
        $default = ucfirst($plural_name . 's');
        return _t($dataObject->ClassName . '.PLURALNAME', $default);
    }
}
