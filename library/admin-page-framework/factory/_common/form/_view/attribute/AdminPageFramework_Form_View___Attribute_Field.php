<?php
/**
 Admin Page Framework v3.7.2b04 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
class AdminPageFramework_Form_View___Attribute_Field extends AdminPageFramework_Form_View___Attribute_FieldContainer_Base {
    public $sContext = 'field';
    protected function _getAttributes() {
        return array('id' => $this->aArguments['_field_container_id'], 'data-type' => $this->aArguments['type'], 'class' => "admin-page-framework-field admin-page-framework-field-" . $this->aArguments['type'] . $this->getAOrB($this->aArguments['attributes']['disabled'], ' disabled', '') . $this->getAOrB($this->aArguments['_is_sub_field'], ' admin-page-framework-subfield', ''));
    }
}