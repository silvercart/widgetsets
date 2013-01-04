<?php
/**
 * extends a widget
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
     * manipulates the cms fields
     *
     * @param  FieldList $fields cms fields
     *
     * @return void
     */
    public function updateCMSFields(FieldList $fields) {
        $manifest    = new SS_ClassManifest(BASE_PATH);
        $descendants = $manifest->getDescendantsOf('Widget');
        $descendants = array_flip($descendants);
        unset($descendants['WidgetSetWidget']);

        foreach ($descendants as $descendant => $index) {
            $descendants[$descendant] = _t($descendant . '.SINGULARNAME', $descendant);
        }

        $fields->push(new DropdownField('ClassName', _t('WidgetSetWidget.TYPE'), $descendants));
    }

}