<?php
/**
 Admin Page Framework v3.7.2b04 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
class AdminPageFramework_FieldType_color extends AdminPageFramework_FieldType {
    public $aFieldTypeSlugs = array('color');
    protected $aDefaultKeys = array('attributes' => array('size' => 10, 'maxlength' => 400, 'value' => 'transparent',),);
    protected function setUp() {
        if (version_compare($GLOBALS['wp_version'], '3.5', '>=')) {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
        } else {
            wp_enqueue_style('farbtastic');
            wp_enqueue_script('farbtastic');
        }
    }
    protected function getStyles() {
        return <<<CSSRULES
/* Color Picker */
.repeatable .colorpicker {
    display: inline;
}
.admin-page-framework-field-color .wp-picker-container {
    vertical-align: middle;
}
.admin-page-framework-field-color .ui-widget-content {
    border: none;
    background: none;
    color: transparent;
}
.admin-page-framework-field-color .ui-slider-vertical {
    width: inherit;
    height: auto;
    margin-top: -11px;
}
.admin-page-framework-field-color .admin-page-framework-repeatable-field-buttons {
    margin-top: 0;
}
.admin-page-framework-field-color .wp-color-result {
    /* Overriding the default css rule, margin: 0 6px 6px 0px; to vertically align middle in the sortable box */
    margin: 3px;
}

CSSRULES;
        
    }
    protected function getScripts() {
        $_aJSArray = json_encode($this->aFieldTypeSlugs);
        $_sDoubleQuote = '\"';
        return <<<JAVASCRIPTS
registerAdminPageFrameworkColorPickerField = function( osTragetInput, aOptions ) {
    
    var osTargetInput   = 'string' === typeof osTragetInput 
        ? '#' + osTragetInput 
        : osTragetInput;
    var sInputID        = 'string' === typeof osTragetInput 
        ? osTragetInput 
        : osTragetInput.attr( 'id' );

    // Only for the iris color picker.
    var _aDefaults = {
        defaultColor: false, // you can declare a default color here, or in the data-default-color attribute on the input     
        change: function(event, ui){}, // a callback to fire whenever the color changes to a valid color. reference : http://automattic.github.io/Iris/     
        clear: function() {}, // a callback to fire when the input is emptied or an invalid color
        hide: true, // hide the color picker controls on load
        palettes: true // show a group of common colors beneath the square or, supply an array of colors to customize further                
    };
    var _aColorPickerOptions = jQuery.extend( {}, _aDefaults, aOptions );
        
    'use strict';
    /* This if-statement checks if the color picker element exists within jQuery UI
     If it does exist, then we initialize the WordPress color picker on our text input field */
    if( 'object' === typeof jQuery.wp && 'function' === typeof jQuery.wp.wpColorPicker ){
        jQuery( osTargetInput ).wpColorPicker( _aColorPickerOptions );
    }
    else {
        /* We use farbtastic if the WordPress color picker widget doesn't exist */
        jQuery( '#color_' + sInputID ).farbtastic( osTargetInput );
    }
}

/* The below function will be triggered when a new repeatable field is added. Since the APF repeater script does not
    renew the color piker element (while it does on the input tag value), the renewal task must be dealt here separately. */
jQuery( document ).ready( function(){
    
    jQuery().registerAdminPageFrameworkCallbacks( {     
        added_repeatable_field: function( node, sFieldType, sFieldTagID, sCallType ) {

            /* If it is not the color field type, do nothing. */
            // if ( jQuery.inArray( sFieldType, $_aJSArray ) <= -1 ) { 
                // return; 
            // }
            
            /* If the input tag is not found, do nothing  */
            var nodeNewColorInput = node.find( 'input.input_color' );
            if ( nodeNewColorInput.length <= 0 ) { 
                return; 
            }
            
            var nodeIris = node.find( '.wp-picker-container' ).first();
            // WP 3.5+
            if ( nodeIris.length > 0 ) { 
                // unbind the existing color picker script in case there is.
                var nodeNewColorInput = nodeNewColorInput.clone(); 
            }
            var sInputID = nodeNewColorInput.attr( 'id' );

            // Reset the value of the color picker
            var sInputValue = nodeNewColorInput.val() 
                ? nodeNewColorInput.val() 
                : nodeNewColorInput.attr( 'data-default' );
            var sInputStyle = sInputValue !== 'transparent' && nodeNewColorInput.attr( 'style' )
                ? nodeNewColorInput.attr( 'style' ) 
                : '';
            nodeNewColorInput.val( sInputValue ); // set the default value    
            nodeNewColorInput.attr( 'style', sInputStyle ); // remove the background color set to the input field ( for WP 3.4.x or below )  

            // Replace the old color picker elements with the new one.
            // WP 3.5+
            if ( nodeIris.length > 0 ) { 
                jQuery( nodeIris ).replaceWith( nodeNewColorInput );
            } 
            // WP 3.4.x -     
            else { 
                node.find( '.colorpicker' ).replaceWith( '<div class=\"colorpicker\" id=\"color_' + sInputID + '\"></div>' );
            }

            // Bind the color picker event.
            registerAdminPageFrameworkColorPickerField( nodeNewColorInput );     
            
        }
    },
    {$_aJSArray}
    );
});
JAVASCRIPTS;
        
    }
    protected function getField($aField) {
        $aField['value'] = is_null($aField['value']) ? 'transparent' : $aField['value'];
        $aField['attributes'] = $this->_getInputAttributes($aField);
        return $aField['before_label'] . "<div class='admin-page-framework-input-label-container'>" . "<label for='{$aField['input_id']}'>" . $aField['before_input'] . ($aField['label'] && !$aField['repeatable'] ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength($aField['label_min_width']) . ";'>" . $aField['label'] . "</span>" : "") . "<input " . $this->getAttributes($aField['attributes']) . " />" . $aField['after_input'] . "<div class='repeatable-field-buttons'></div>" . "</label>" . "<div class='colorpicker' id='color_{$aField['input_id']}'></div>" . $this->_getColorPickerEnablerScript("{$aField['input_id']}") . "</div>" . $aField['after_label'];
    }
    private function _getInputAttributes(array $aField) {
        return array('color' => $aField['value'], 'value' => $aField['value'], 'data-default' => isset($aField['default']) ? $aField['default'] : 'transparent', 'type' => 'text', 'class' => trim('input_color ' . $aField['attributes']['class']),) + $aField['attributes'];
    }
    private function _getColorPickerEnablerScript($sInputID) {
        $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function(){
    registerAdminPageFrameworkColorPickerField( '{$sInputID}' );
});            
JAVASCRIPTS;
        return "<script type='text/javascript' class='color-picker-enabler-script'>" . '/* <![CDATA[ */' . $_sScript . '/* ]]> */' . "</script>";
    }
}