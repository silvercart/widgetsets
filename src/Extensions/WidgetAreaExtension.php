<?php

namespace WidgetSets\Extensions;

use SilverStripe\ORM\DataExtension;
use WidgetSets\Model\WidgetSet;

/**
 * Extension for WidgetArea.
 *
 * @package WidgetSets
 * @subpackage Extensions
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.07.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class WidgetAreaExtension extends DataExtension {
    
    /**
     * Has one back relation.
     *
     * @var array
     */
    private static $belongs_to = [
        'WidgetSet' => WidgetSet::class,
    ];
    
}