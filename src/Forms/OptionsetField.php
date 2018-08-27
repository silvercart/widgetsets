<?php

namespace WidgetSets\Forms;

use SilverStripe\Forms\OptionsetField as SilverStripeOptionsetField;
use SilverStripe\View\ArrayData;

/**
 * Special OptionsetField to optimize the creation of a widget.
 * 
 * @package WidgetSets
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.08.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class OptionsetField extends SilverStripeOptionsetField
{
    /**
     * Descriptions for each option value.
     *
     * @var array
     */
    protected $optionDescriptions = [];
    
    /**
     * Sets the descriptions for each option value.
     * 
     * @param array $optionDescriptions Option value descriptions
     * 
     * @return $this
     */
    public function setOptionDescriptions($optionDescriptions)
    {
        $this->optionDescriptions = $optionDescriptions;
        return $this;
    }
    
    /**
     * Returns the descriptions for each option value.
     * 
     * @return array
     */
    public function getOptionDescriptions()
    {
        return $this->optionDescriptions;
    }
    
    /**
     * Sets the description for the given option value.
     * 
     * @param string $option            Option value to set description for
     * @param string $optionDescription Option value description
     * 
     * @return $this
     */
    public function setOptionDescription($option, $optionDescription)
    {
        $this->optionDescriptions[$option] = $optionDescription;
        return $this;
    }
    
    /**
     * Returns the descriptions for the given option value.
     * 
     * @param string $option Option value to get description for
     * 
     * @return array
     */
    public function getOptionDescription($option)
    {
        return $this->optionDescriptions[$option];
    }

    /**
     * Build a field option for template rendering
     *
     * @param mixed   $value Value of the option
     * @param string  $title Title of the option
     * @param boolean $odd   True if this should be striped odd. Otherwise it should be striped even
     * 
     * @return ArrayData Field option
     */
    protected function getFieldOption($value, $title, $odd)
    {
        return ArrayData::create([
            'ID'          => $this->getOptionID($value),
            'Class'       => $this->getOptionClass($value, $odd),
            'Name'        => $this->getOptionName(),
            'Value'       => $value,
            'Title'       => $title,
            'isChecked'   => $this->isSelectedValue($value, $this->Value()),
            'isDisabled'  => $this->isDisabledValue($value),
            'Description' => $this->getOptionDescription($value),
        ]);
    }
}