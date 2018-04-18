<?php

namespace WidgetSets\Admin\Controllers;

use SilverStripe\Admin\ModelAdmin;
use WidgetSets\Model\WidgetSet;

/**
 * ModelAdmin for WidgetSets.
 *
 * @package WidgetSets
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class WidgetSetAdmin extends ModelAdmin {

    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'widget-sets';

    /**
     * The menu title
     *
     * @var string
     */
    private static $menu_title = 'Widget Sets';

    /**
     * Managed models
     *
     * @var array
     */
    private static $managed_models = array(
        WidgetSet::class,
    );

    /**
     * Menu icon
     *
     * @var string
     */
    private static $menu_icon = 'silvercart/img/glyphicons-halflings.png';

    /**
     * We don't want the import form here.
     *
     * @var boolean
     */
    public $showImportForm = false;

    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 04.01.2013
     */
    public function init() {
        parent::init();
        $this->extend('updateInit');
    }
    
    /**
     * title in the upper bar of the CMS
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 11.01.2013
     */
    public function SectionTitle() {
        return _t(WidgetSet::class . '.PLURALNAME', 'Widget Sets');
    }
}


