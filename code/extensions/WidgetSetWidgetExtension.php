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
 * extends a {@link Widget}
 *
 * @package Widgetsets
 * @subpackage Extensions
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 04.01.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class WidgetSetWidgetExtension extends DataExtension {

    /**
     * array which holds all classnames of widgets which should
     * not be able to create in a widgetset
     * 
     * @var array
     */
    private static $hiddenWidgets = array();

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
            $manifest    = new SS_ClassManifest(BASE_PATH);
            $descendants = $manifest->getDescendantsOf('Widget');
            $descendants = array_flip($descendants);
            
            foreach (self::$hiddenWidgets as $className) {
                unset($descendants[$className]);
            }

            foreach ($descendants as $descendant => $index) {
                $descendants[$descendant] = _t($descendant . '.TITLE', $descendant);
            }
            $fields->push(new DropdownField('ClassName', _t('WidgetSetWidget.TYPE'), $descendants));
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
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 21.02.2013
     */
    public static function preventWidgetCreationByClass($widgetClass = null) {
        if (!is_null($widgetClass)) {
            self::$hiddenWidgets = array_merge(
                self::$hiddenWidgets, 
                array(
                    $widgetClass
                )
            );
        }
    }

}