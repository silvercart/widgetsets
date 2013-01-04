<?php

class WidgetSetPageExtension extends DataExtension {

    /**
     * array to add many_many relations
     *
     * @var array
     */
    public static $many_many = array(
        'WidgetSetSidebar' => 'WidgetSet',
        'WidgetSetContent' => 'WidgetSet',
    );

    /**
     * updates cms fields and adds widgetset gridfields
     *
     * @param  FieldList $fields fields
     *
     * @return void
     */
    public function updateCMSFields(FieldList $fields) {
        // create the configuration for the grid fields
        $config = GridFieldConfig_RelationEditor::create();

        // ...and create them
        $widgetSetSidebarLabel = new HeaderField('WidgetSetSidebarLabel', _t('WidgetSetWidgets.WIDGETSET_SIDEBAR_FIELD_LABEL'));
        $widgetSetSidebarField = new GridField("WidgetSetSidebar", "Sidebar widgets", $this->owner->WidgetSetSidebar(), $config);

        $widgetSetContentlabel = new HeaderField('WidgetSetContentLabel', _t('WidgetSetWidgets.WIDGETSET_CONTENT_FIELD_LABEL', 'Content'));
        $widgetSetContentField = new GridField("WidgetSetContent", "Sidebar widgets", $this->owner->WidgetSetContent(), $config);

        $fields->addFieldToTab("Root.Widgets", $widgetSetSidebarLabel);
        $fields->addFieldToTab("Root.Widgets", $widgetSetSidebarField);
        # $fields->addFieldToTab("Root.Widgets", $widgetSetContentlabel);
        $fields->addFieldToTab("Root.Widgets", $widgetSetContentField);
    }
}