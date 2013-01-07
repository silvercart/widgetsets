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
 * @subpackage Base
 */

/**
 * Provides methods for common tasks.
 *
 * @package Widgetsets
 * @subpackage Base
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 04.01.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class WidgetSetTools extends Object {

    /**
     * returns a single page by IdentifierCode
     * used to retrieve links dynamically
     *
     * @param string $identifierCode the classes name
     *
     * @return SiteTree | false a single object of the site tree; without param the SilvercartFrontPage will be returned
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 04.01.2013
     */
    public function PageByIdentifierCode($identifierCode = "PixeltricksHomePage") {
        $page = Page::get()->filter('IdentifierCode', $identifierCode)->First();

        if ($page) {
            return $page;
        } else {
            return false;
        }
    }

    /**
     * returns a page link by IdentifierCode
     *
     * @param string $identifierCode the DataObjects IdentifierCode
     *
     * @return string
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 04.01.2013
     */
    public function PageByIdentifierCodeLink($identifierCode = "PixeltricksHomePage") {
        $page = $this->PageByIdentifierCode($identifierCode);
        if ($page === false) {
            return '';
        }
        return $page->Link();
    }

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
        if (_t($dataObject->ClassName . '.SINGULARNAME')) {
            return _t($dataObject->ClassName . '.SINGULARNAME');
        } else {
            return ucwords(trim(strtolower(preg_replace('/_?([A-Z])/', ' $1', $dataObject->class))));;
        }
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
        if (_t($dataObject->ClassName . '.PLURALNAME')) {
            return _t($dataObject->ClassName . '.PLURALNAME');
        } else {
            $plural_name = self::singular_name_for($dataObject);
            if (substr($plural_name,-1) == 'e') {
                $plural_name = substr($plural_name,0,-1);
            } elseif (substr($plural_name,-1) == 'y') {
                $plural_name = substr($plural_name,0,-1) . 'ie';
            }
            return ucfirst($plural_name . 's');
        }
    }
}
