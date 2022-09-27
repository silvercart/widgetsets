<?php

namespace WidgetSets\Admin\Controllers;

use SilverStripe\Admin\ModelAdmin;
use WidgetSets\Model\WidgetSet;

/**
 * ModelAdmin for WidgetSets.
 *
 * @package WidgetSets
 * @subpackage Admin\Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.08.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class WidgetSetAdmin extends ModelAdmin
{
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
     * Menu icon
     * 
     * @var string
     */
    private static $menu_icon = null;
    /**
     * Menu icon CSS class
     * 
     * @var string
     */
    private static $menu_icon_class = 'font-icon-block-layout';
    /**
     * Managed models
     *
     * @var array
     */
    private static $managed_models = [
        WidgetSet::class,
    ];
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.08.2018
     */
    public function init()
    {
        parent::init();
        $this->extend('updateInit');
    }
    
    /**
     * title in the upper bar of the CMS
     *
     * @return string 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.08.2018
     */
    public function SectionTitle()
    {
        return _t(WidgetSet::class . '.PLURALNAME', 'Widget Sets');
    }
}


