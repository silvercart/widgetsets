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
 * @subpackage Config
 */
if (!class_exists('SS_Object')) {
    class_alias('Object', 'SS_Object');
}

SiteTree::add_extension('WidgetSetPageExtension');
ContentController::add_extension('WidgetSetPageExtension_Controller');
Widget::add_extension('WidgetSetWidgetExtension');

WidgetSetWidgetExtension::preventWidgetCreationByClass('WidgetSetWidget');