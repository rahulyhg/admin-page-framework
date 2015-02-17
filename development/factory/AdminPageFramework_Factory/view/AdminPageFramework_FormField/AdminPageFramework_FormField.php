<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for rendering form input fields.
 *
 * @since       2.0.0
 * @since       2.0.1       Added the <em>size</em> type.
 * @since       2.1.5       Separated the methods that defines field types to different classes.
 * @extends     AdminPageFramework_FormField_Base
 * @package     AdminPageFramework
 * @subpackage  Form
 * @internal
 */
class AdminPageFramework_FormField extends AdminPageFramework_FormField_Base {
            
    /**
     * Returns the input tag name for the name attribute.
     * 
     * @since   2.0.0
     * @since   3.0.0       Dropped the page slug dimension. Deprecated the 'name' field key to override the name attribute since the new 'attribute' key supports the functionality.
     * @since   3.2.0       Added the $hfFilterCallback parameter.
     */
    private function _getInputName( $aField=null, $sKey='', $hfFilterCallback=null ) {
        
        $sKey           = ( string ) $sKey; // casting string is required as 0 value may have been interpreted as false.
        $aField         = isset( $aField ) ? $aField : $this->aField;
        $_sKey          = '0' !== $sKey && empty( $sKey ) ? '' : "[{$sKey}]";
        $_sSectionIndex = isset( $aField['section_id'], $aField['_section_index'] ) ? "[{$aField['_section_index']}]" : "";
        $_sResult       = '';
        $_sResultTail   = '';
        switch( $aField['_fields_type'] ) {
            default:
            case 'page':
                $sSectionDimension = isset( $aField['section_id'] ) && $aField['section_id'] && '_default' != $aField['section_id']
                    ? "[{$aField['section_id']}]"
                    : '';
                $_sResult = "{$aField['option_key']}{$sSectionDimension}{$_sSectionIndex}[{$aField['field_id']}]{$_sKey}";
                break;
                
            case 'page_meta_box':
            case 'post_meta_box':
                $_sResult = isset( $aField['section_id'] ) && $aField['section_id'] && '_default' != $aField['section_id']
                    ? "{$aField['section_id']}{$_sSectionIndex}[{$aField['field_id']}]{$_sKey}"
                    : "{$aField['field_id']}{$_sKey}";
                break;
                
            // taxonomy fields type does not support sections.
            case 'taxonomy': 
                $_sResult = "{$aField['field_id']}{$_sKey}";
                break;
                
            // 3.2.0+, 3.5.2+ Fixed a bug that section names were not set properly
            // This one is tricky as the core widget factory method enclose this value in []. So when the framework field has a section, it must not end with ].
            case 'widget':      
                $_sResult       = isset( $aField['section_id'] ) && $aField['section_id'] && '_default' != $aField['section_id']
                    ? "{$aField['section_id']}]{$_sSectionIndex}[{$aField['field_id']}"
                    : "{$aField['field_id']}";
                $_sResultTail   = $_sKey;                  
                break;            
                
            case 'user_meta':   // 3.5.0+
                $_sResult       = isset( $aField['section_id'] ) && $aField['section_id'] && '_default' != $aField['section_id']
                    ? "{$aField['section_id']}{$_sSectionIndex}[{$aField['field_id']}]"
                    : "{$aField['field_id']}";            
                $_sResultTail   = $_sKey;
                break;
                
        }
        return is_callable( $hfFilterCallback )
            ? call_user_func_array( $hfFilterCallback, array( $_sResult ) ) . $_sResultTail
            : $_sResult . $_sResultTail;
            
    }
        
    /**
     * Retrieves the field name attribute whose dimensional elements are delimited by the pile character.
     * 
     * Instead of [] enclosing array elements, it uses the pipe(|) to represent the multi dimensional array key.
     * This is used to create a reference to the submit field name to determine which button is pressed.
     * 
     * @remark  Used by the import and submit field types.
     * @since   2.0.0
     * @since   2.1.5       Made the parameter mandatory. Changed the scope to protected from private. Moved from AdminPageFramework_FormField.
     * @since   3.0.0       Moved from the submit field type class. Dropped the page slug dimension.
     * @since   3.2.0       Added the $hfFilterCallback parameter.
     */ 
    protected function _getFlatInputName( $aField, $sKey='', $hfFilterCallback=null ) {    
        
        $sKey           = ( string ) $sKey; // casting string is important as 0 value may have been interpreted as false.
        $_sKey          = '0' !== $sKey && empty( $sKey ) ? '' : "|{$sKey}";
        $_sSectionIndex = isset( $aField['section_id'], $aField['_section_index'] ) ? "|{$aField['_section_index']}" : "";
        $_sResult       = '';
        $_sResultTail   = '';
        switch( $aField['_fields_type'] ) {
            default:
            case 'page':
                $sSectionDimension = isset( $aField['section_id'] ) && $aField['section_id'] && '_default' != $aField['section_id']
                    ? "|{$aField['section_id']}"
                    : '';
                $_sResult = "{$aField['option_key']}{$sSectionDimension}{$_sSectionIndex}|{$aField['field_id']}{$_sKey}";
                break;
                
            case 'page_meta_box':
            case 'post_meta_box':
                $_sResult = isset( $aField['section_id'] ) && $aField['section_id'] && '_default' != $aField['section_id']
                    ? "{$aField['section_id']}{$_sSectionIndex}|{$aField['field_id']}{$_sKey}"
                    : "{$aField['field_id']}{$_sKey}";
                break;
                
            // taxonomy fields type does not support sections.
            case 'taxonomy': 
                $_sResult = "{$aField['field_id']}{$_sKey}";
                break;
            
            case 'widget':      // 3.2.0+                
            case 'user_meta':   // 3.5.0+            
                $_sResult       = isset( $aField['section_id'] ) && $aField['section_id'] && '_default' != $aField['section_id']
                    ? "{$aField['section_id']}{$_sSectionIndex}|{$aField['field_id']}"
                    : "{$aField['field_id']}";            
                $_sResultTail   = $_sKey;
                break;
                
        }    
        return is_callable( $hfFilterCallback )
            ? call_user_func_array( $hfFilterCallback, array( $_sResult ) ) . $_sResultTail
            : $_sResult . $_sResultTail;    
            
    }
        
    /**
     * Returns the input tag ID.
     * 
     * e.g. "{$aField['field_id']}__{$isIndex}";
     * 
     * @remark      The index keys are prefixed with double-underscores.
     * @since       2.0.0
     * @since       3.2.0       Added the $hfFilterCallback parameter.
     * @since       3.3.2       Made it static public because the `<for>` tag needs to refer to it and it is called from another class that renders the form table. Added a default value for the <var>$isIndex</var> parameter.
     */
    static public function _getInputID( $aField, $isIndex=0, $hfFilterCallback=null ) {
        
        $_sSectionIndex  = isset( $aField['_section_index'] ) ? '__' . $aField['_section_index'] : ''; // double underscore
        $_isFieldIndex   = '__' . $isIndex; // double underscore
        $_sResult        = isset( $aField['section_id'] ) && '_default' != $aField['section_id']
            ? $aField['section_id'] . $_sSectionIndex . '_' . $aField['field_id'] . $_isFieldIndex
            : $aField['field_id'] . $_isFieldIndex;
        return is_callable( $hfFilterCallback )
            ? call_user_func_array( $hfFilterCallback, array( $_sResult ) )
            : $_sResult;            
            
    }
    

    /**
     * Returns the field input base ID used for field container elements.
     * 
     * The returning value does not represent the exact ID of the field input tag. 
     * This is because each input tag has an index for sub-fields.
     * 
     * @remark  This is called from the fields table class to insert the row id.
     * @since   2.0.0
     * @since   3.2.0       Added the $hfFilterCallback parameter.
     * @since   3.3.2       Changed the name from `_getInputTagID()`.
     */
    static public function _getInputTagBaseID( $aField, $hfFilterCallback=null )  {
        
        $_sSectionIndex = isset( $aField['_section_index'] ) ? '__' . $aField['_section_index'] : '';
        $_sResult       = isset( $aField['section_id'] ) && '_default' != $aField['section_id']
            ? $aField['section_id'] . $_sSectionIndex . '_' . $aField['field_id']
            : $aField['field_id'];
        return is_callable( $hfFilterCallback )
            ? call_user_func_array( $hfFilterCallback, array( $_sResult ) )
            : $_sResult;        
            
    }     
    
    /** 
     * Retrieves the input field HTML output.
     * @since       2.0.0
     * @since       2.1.6       Moved the repeater script outside the fieldset tag.
     */ 
    public function _getFieldOutput() {
        
        $_aFieldsOutput = array(); 

        /* 1. Prepend the field error message. */
        $_sFieldError = $this->_getFieldError( $this->aErrors, $this->aField['section_id'], $this->aField['field_id'] );
        if ( '' !== $_sFieldError ) {
            $_aFieldsOutput[] = $_sFieldError;
        }
                    
        /* 2. Set the tag ID used for the field container HTML tags. */
        $this->aField['tag_id'] = $this->_getInputTagBaseID( $this->aField, $this->aCallbacks['hfTagID'] );
            
        /* 3. Construct fields array for sub-fields */
        $_aFields = $this->_constructFieldsArray( $this->aField, $this->aOptions );

        /* 4. Get the field and its sub-fields output. */
        $_aFieldsOutput[] = $this->_getFieldsOutput( $_aFields, $this->aCallbacks );
                    
        /* 5. Return the entire output */
        return $this->_getFinalOutput( $this->aField, $_aFieldsOutput, count( $_aFields ) );

    }
    
        /**
         * Returns the output of the given fieldset (main field and its sub-fields) array.
         * 
         * @since   3.1.0
         * @since   3.2.0   Added the $aCallbacks parameter.
         */ 
        private function _getFieldsOutput( array $aFields, array $aCallbacks=array() ) {

            $_aOutput = array();
            foreach( $aFields as $_isIndex => $_aField ) {

                $_aOutput[] = $this->_getEachFieldOutput( 
                    $_aField, 
                    $_isIndex, 
                    $aCallbacks,
                    $this->isLastElement( $aFields, $_isIndex )
                );

            }     
            
            return implode( PHP_EOL, array_filter( $_aOutput ) );
            
        }
        
            /**
             * Returns the HTML output of the given field.
             * @internal
             * @since       3.5.3
             * @return      string      the HTML output of the given field.
             */
            private function _getEachFieldOutput( array $aField, $isIndex, array $aCallbacks, $bIsLastElement=false ) {
                
                // Field type definition - allows mixed field types in sub-fields 
                $_aFieldTypeDefinition = $this->_getFieldTypeDefinition( $aField['type'] );
                if ( ! is_callable( $_aFieldTypeDefinition['hfRenderField'] ) ) {
                    return '';
                }     

                // Set some internal keys 
                $aField = $this->_getFormatedFieldDefinitionArray( $aField, $isIndex, $aCallbacks, $_aFieldTypeDefinition );
                
                // Callback the registered function to output the field 
                $_aFieldAttributes = $this->_getFieldAttributes( $aField );
                            
                return $aField['before_field']
                    . "<div " . $this->_getFieldContainerAttributes( $aField, $_aFieldAttributes, 'field' ) . ">"
                        . call_user_func_array(
                            $_aFieldTypeDefinition['hfRenderField'],
                            array( $aField )
                        )
                        . $this->_getDelimiter( $aField, $bIsLastElement )
                    . "</div>"
                    . $aField['after_field'];                
                
            }
                /**
                 * Returns the registered field type definition array of the given field type slug.
                 * 
                 * @remark      The $this->aFieldTypeDefinitions property stores default key-values of all the registered field types.
                 * @internal
                 * @since       3.5.3
                 * @return      array   The field type definition array.
                 */
                private function _getFieldTypeDefinition( $sFieldTypeSlug ) {
                    return isset( $this->aFieldTypeDefinitions[ $sFieldTypeSlug ] )
                        ? $this->aFieldTypeDefinitions[ $sFieldTypeSlug ] 
                        : $this->aFieldTypeDefinitions['default'];
                }  
                /**
                 * Returns the formatted field definition array.
                 * @internal
                 * @since       3.5.3
                 * @return      array       The formatted field definition array.
                 */
                private function _getFormatedFieldDefinitionArray( array $aField, $isIndex, array $aCallbacks, $aFieldTypeDefinition ) {

                    $_bIsSubField                         = is_numeric( $isIndex ) && 0 < $isIndex;
                    $aField['_is_sub_field']              = $_bIsSubField;      // 3.5.3+
                    $aField['_index']                     = $isIndex;
                    $aField['input_id']                   = $this->_getInputID( $aField, $isIndex, $aCallbacks['hfID'] ); //  ({section id}_){field_id}_{index}
                    $aField['_input_name']                = $this->_getInputName( $aField, $aField['_is_multiple_fields'] ? $isIndex : '', $aCallbacks['hfName'] );    
                    $aField['_input_name_flat']           = $this->_getFlatInputName( $aField, $aField['_is_multiple_fields'] ? $isIndex : '', $aCallbacks['hfNameFlat'] ); // used for submit, export, import field types     
                    $aField['_field_container_id']        = "field-{$aField['input_id']}"; // used in the attribute below plus it is also used in the sample custom field type.

                        // @todo for issue #158 https://github.com/michaeluno/admin-page-framework/issues/158               
                        // These models are for generating ids and names dynamically.
                        $aField['_input_id_model']            = $this->_getInputID( $aField, '-fi-',  $aCallbacks['hfID'] ); // 3.3.1+ referred by the repeatable field script
                        $aField['_input_name_model']          = $this->_getInputName( $aField, $aField['_is_multiple_fields'] ? '-fi-': '', $aCallbacks['hfName'] );      // 3.3.1+ referred by the repeatable field script
                        $aField['_fields_container_id_model'] = "field-{$aField['_input_id_model']}"; // [3.3.1+] referred by the repeatable field script
                        
                    $aField['_fields_container_id']       = "fields-{$this->aField['tag_id']}";
                    $aField['_fieldset_container_id']     = "fieldset-{$this->aField['tag_id']}";
                    $aField                               = $this->uniteArrays(
                        $aField, // includes the user-set values.
                        array( // the automatically generated values.
                            'attributes' => array(
                                'id'                => $aField['input_id'],
                                'name'              => $aField['_input_name'],
                                'value'             => $aField['value'],
                                'type'              => $aField['type'], // text, password, etc.
                                'disabled'          => null,
                                'data-id_model'     => $aField['_input_id_model'],    // 3.3.1+
                                'data-name_model'   => $aField['_input_name_model'],  // 3.3.1+
                            )
                        ),
                        ( array ) $aFieldTypeDefinition['aDefaultKeys'] // this allows sub-fields with different field types to set the default key-values for the sub-field.
                    );
                    
                    $aField['attributes']['class'] = 'widget' === $aField['_fields_type'] && is_callable( $aCallbacks['hfClass'] )
                        ? call_user_func_array( $aCallbacks['hfClass'], array( $aField['attributes']['class'] ) )
                        : $aField['attributes']['class'];
                    $aField['attributes']['class'] = $this->generateClassAttribute(
                        $aField['attributes']['class'],  
                        $this->dropElementsByType( $aField['class'] )
                    );
                    return $aField;
                    
                }
                /**
                 * Returns the field container attribute array.
                 * 
                 * @remark      _getFormatedFieldDefinitionArray() should be performed prior to callign this method.
                 * @param       array       $aField     The field definition array. This should have been formatted already witjh the `_getFormatedFieldDefinitionArray()` method.
                 * @return      array       The generated field container attribute array.
                 * @internal   
                 * @since       3.5.3
                 */
                private function _getFieldAttributes( array $aField ) {            
                    return array(
                        'id'            => $aField['_field_container_id'],
                        'data-type'     => "{$aField['type']}",   // this is referred by the repeatable field JavaScript script.
                        'data-id_model' => $aField['_fields_container_id_model'], // 3.3.1+
                        'class'         => "admin-page-framework-field admin-page-framework-field-{$aField['type']}" 
                            . ( $aField['attributes']['disabled'] ? ' disabled' : null )
                            . ( $aField['_is_sub_field'] ? ' admin-page-framework-subfield' : null ),
                    );
                }     
                /**
                 * Returns the HTML output of delimiter
                 * @internal
                 * @since       3.5.3
                 * @return      string      the HTML output of delimiter
                 */
                private function _getDelimiter( array $aField, $bIsLastElement ) {
                    return ! $aField['delimiter']
                        ? ''
                        : "<div " . $this->generateAttributes( 
                            array(
                                'class' => 'delimiter',
                                'id'    => "delimiter-{$aField['input_id']}",
                                'style' => $bIsLastElement ? "display:none;" : "",
                            ) ) . ">"
                                . $aField['delimiter']
                            . "</div>";
                }                
                
        /**
         * Returns the final fields output.
         * 
         * @since 3.1.0
         */
        private function _getFinalOutput( array $aField, array $aFieldsOutput, $iFieldsCount ) {
                            
            // Construct attribute arrays.
            
            // the 'fieldset' container attributes
            $_aFieldsSetAttributes = array(
                'id'            => 'fieldset-' . $aField['tag_id'],
                'class'         => 'admin-page-framework-fieldset',
                'data-field_id' => $aField['tag_id'], // <-- don't remember what this was for...
            );
            
            // the 'fields' container attributes
            $_aFieldsContainerAttributes = array(
                'id'            => 'fields-' . $aField['tag_id'],
                'class'         => 'admin-page-framework-fields'
                    . ( $aField['repeatable'] ? ' repeatable' : '' )
                    . ( $aField['sortable'] ? ' sortable' : '' ),
                'data-type'     => $aField['type'], // this is referred by the sortable field JavaScript script.
            );
            
            return $aField['before_fieldset']
                . "<fieldset " . $this->_getFieldContainerAttributes( $aField, $_aFieldsSetAttributes, 'fieldset' ) . ">"
                    . "<div " . $this->_getFieldContainerAttributes( $aField, $_aFieldsContainerAttributes, 'fields' ) . ">"
                        . $aField['before_fields']
                            . implode( PHP_EOL, $aFieldsOutput )
                        . $aField['after_fields']
                    . "</div>"
                    . $this->_getExtras( $aField, $iFieldsCount )
                . "</fieldset>"
                . $aField['after_fieldset'];
                        
        }
            /**
             * Returns the output of the extra elements for the fields such as description and JavaScri
             * 
             * The additional but necessary elements are placed outside of the fields tag. 
             */
            private function _getExtras( $aField, $iFieldsCount ) {
                
                $_aOutput = array();
                
                // Add the description
                if ( isset( $aField['description'] ) )  {
                    $_aOutput[] = $this->_getDescription( $aField['description'] );
                }
                    
                // Add the repeater & sortable scripts 
                $_aOutput[] = $this->_getFieldScripts( $aField, $iFieldsCount );
                
                return implode( PHP_EOL, $_aOutput );
                
            }
                /**
                 * Returns the HTML formatted description blocks by the given description definition.
                 * 
                 * @since   3.3.0
                 * @return  string      The description output.
                 */
                private function _getDescription( $asDescription ) {
                    
                    if ( empty( $asDescription ) ) { return ''; }
                    
                    $_aOutput = array();
                    foreach( $this->getAsArray( $asDescription ) as $_sDescription ) {
                        $_aOutput[] = "<p class='admin-page-framework-fields-description'>"
                                . "<span class='description'>{$_sDescription}</span>"
                            . "</p>";
                    }
                    return implode( PHP_EOL, $_aOutput );
                    
                }
                /**
                 * Returns the output of JavaScript scripts for the field (and its sub-fields).
                 * 
                 * @since 3.1.0
                 */
                private function _getFieldScripts( $aField, $iFieldsCount ) {
                    
                    $_aOutput = array();
                    
                    // Add the repeater script 
                    $_aOutput[] = $aField['repeatable']
                        ? $this->_getRepeaterFieldEnablerScript( 'fields-' . $aField['tag_id'], $iFieldsCount, $aField['repeatable'] )
                        : '';

                    // Add the sortable script - if the number of fields is only one, no need to sort the field. 
                    // Repeatable fields can make the number increase so here it checkes the repeatability.
                    $_aOutput[] = $aField['sortable'] && ( $iFieldsCount > 1 || $aField['repeatable'] )
                        ? $this->_getSortableFieldEnablerScript( 'fields-' . $aField['tag_id'] )
                        : '';     
                    
                    return implode( PHP_EOL, $_aOutput );
                    
                }
        
        /**
         * Returns the set field error message to the section or field.
         * 
         * @since       3.1.0
         * @return      string     The error string message. An empty value if not found.
         */
        private function _getFieldError( $aErrors, $sSectionID, $sFieldID ) {
            
            // If this field has a section and the error element is set
            if ( 
                isset( 
                    $aErrors[ $sSectionID ], 
                    $aErrors[ $sSectionID ][ $sFieldID ]
                )
                && is_array( $aErrors[ $sSectionID ] )
                && ! is_array( $aErrors[ $sSectionID ][ $sFieldID ] )
                
            ) {     
                return "<span class='field-error'>*&nbsp;{$this->aField['error_message']}" 
                        . $aErrors[ $sSectionID ][ $sFieldID ]
                    . "</span>";
            } 
            
            // if this field does not have a section and the error element is set,
            if ( isset( $aErrors[ $sFieldID ] ) && ! is_array( $aErrors[ $sFieldID ] ) ) {
                return "<span class='field-error'>*&nbsp;{$this->aField['error_message']}" 
                        . $aErrors[ $sFieldID ]
                    . "</span>";
            }  
            
            return '';
            
        }    
            
        /**
         * Returns the array of fields 
         * 
         * @since       3.0.0
         */
        protected function _constructFieldsArray( &$aField, &$aOptions ) {

            // Get the set value(s)
            $_mSavedValue    = $this->_getStoredInputFieldValue( $aField, $aOptions );
            
            // Construct fields array.
            $_aFields = $this->_getFieldsWithSubs( $aField, $_mSavedValue );
                 
            // Set the saved values
            $this->_setSavedFieldsValue( $_aFields, $_mSavedValue, $aField );

            // Determine the value
            $this->_setFieldsValue( $_aFields ); // by reference

            return $_aFields;
            
        }
            /**
             * Returns fields array which includes sub-fields.
             * 
             * @since       3.5.3
             */
            private function _getFieldsWithSubs( array $aField, $mSavedValue ) {

                // Separate the first field and sub-fields
                $aFirstField    = array();
                $aSubFields     = array();
                
                // $aFirstField and $aSubFields get updated in the method
                $this->_divideMainAndSubFields( $aField, $aFirstField, $aSubFields );
                            
                // $aSubFields gets updated in the method
                $this->_fillRepeatableElements( $aField, $aSubFields, $mSavedValue );
                                
                 // $aSubFields gets updated in the method
                $this->_fillSubFields( $aSubFields, $aFirstField );

                // Put them together
                return array_merge( array( $aFirstField ), $aSubFields );
                
            }            
                /**
                 * Divide the fields into the main field and sub fields.
                 * 
                 * @remark      The method will update the arrays passed to the second and the third parameter.
                 * @since       3.5.3
                 * @internal
                 * @return      void
                 */
                private function _divideMainAndSubFields( array $aField, array &$aFirstField, array &$aSubFields ) {
                    foreach( $aField as $_nsIndex => $_mFieldElement ) {
                        if ( is_numeric( $_nsIndex ) ) {
                            $aSubFields[] = $_mFieldElement;
                        } else {
                            $aFirstField[ $_nsIndex ] = $_mFieldElement;
                        }
                    }     
                }   
                /**
                 * Fills sub-fields with repeatable fields.
                 * 
                 * This method creates the sub-fields of repeatable fields based on the saved values.
                 * 
                 * @remark      This method updates the passed array to the second parameter.
                 * @sicne       3.5.3
                 * @internal
                 * @return      void
                 */
                private function _fillRepeatableElements( array $aField, array &$aSubFields, $mSavedValue ) {
                    if ( ! $aField['repeatable'] ) {
                        return;
                    }
                    $_aSavedValue = ( array ) $mSavedValue;
                    unset( $_aSavedValue[ 0 ] );
                    foreach( $_aSavedValue as $_iIndex => $vValue ) {
                        $aSubFields[ $_iIndex - 1 ] = isset( $aSubFields[ $_iIndex - 1 ] ) && is_array( $aSubFields[ $_iIndex - 1 ] ) 
                            ? $aSubFields[ $_iIndex - 1 ] 
                            : array();     
                    }       
                }
                /**
                 * Fillds sub-fields.
                 * @since       3.5.3
                 * @internal
                 * @return      void
                 */
                private function _fillSubFields( array &$aSubFields, array $aFirstField ) {                
                            
                    foreach( $aSubFields as &$_aSubField ) {
                        
                        // Evacuate the label element which should not be merged.
                        $_aLabel = isset( $_aSubField['label'] ) 
                            ? $_aSubField['label']
                            : ( isset( $aFirstField['label'] )
                                 ? $aFirstField['label'] 
                                 : null
                            );
                        
                        // Do recursive array merge - the 'attributes' array of some field types have more than one dimensions.
                        $_aSubField = $this->uniteArrays( $_aSubField, $aFirstField ); 
                        
                        // Restore the label element.
                        $_aSubField['label'] = $_aLabel;
                        
                    }
                }
                
            /**
             * Sets saved field values to the given field arrays.
             * 
             * @since       3.5.3
             */
            private function _setSavedFieldsValue( array &$aFields, $mSavedValue, $aField ) {
             
                // Determine whether the elements are saved in an array.
                $_bHasSubFields = count( $aFields ) > 1 || $aField['repeatable'] || $aField['sortable'];
                if ( ! $_bHasSubFields ) {
                    $aFields[ 0 ]['_saved_value'] = $mSavedValue;
                    $aFields[ 0 ]['_is_multiple_fields'] = false;
                    return;                    
                }
         
                foreach( $aFields as $_iIndex => &$_aThisField ) {
                    $_aThisField['_saved_value'] = isset( $mSavedValue[ $_iIndex ] ) 
                        ? $mSavedValue[ $_iIndex ] 
                        : null;
                    $_aThisField['_is_multiple_fields'] = true;
                }
        
            } 
            
            /**
             * Sets the value to the given fields array.
             * 
             * @since       3.5.3
             */
            private function _setFieldsValue( array &$aFields ) {
                foreach( $aFields as &$_aField ) {
                    $_aField['_is_value_set_by_user'] = isset( $_aField['value'] );
                    $_aField['value']                 = $this->_getSetFieldValue( $_aField );
                }
            }
            /**
             * Returns the set field value.
             * 
             * @since       3.5.3
             */
            private function _getSetFieldValue( array $aField ) {
                
                if ( isset( $aField['value'] ) ) {
                    return $aField['value'];
                }
                if ( isset( $aField['_saved_value'] ) ) {
                    return $aField['_saved_value'];
                }
                if ( isset( $aField['default'] ) ) {
                    return $aField['default'];
                }
                return null;                  
                
            }            
            /**
             * Returns the stored field value.
             * 
             * It checks if a previously saved option value exists or not. Regular setting pages and page meta boxes will be applied here.
             * It's important to return null if not set as the returned value will be checked later on whether it is set or not. If an empty value is returned, they will think it's set.
             * 
             * @since       2.0.0
             * @since       3.0.0       Removed the check of the 'value' and 'default' keys. Made it use the '_fields_type' internal key.
             * @since       3.1.0       Changed the name to _getStoredInputFieldValue from _getInputFieldValue
             * @since       3.4.1       Removed the switch block as it was redundant.
             */
            private function _getStoredInputFieldValue( $aField, $aOptions ) {    

                // If a section is not set, check the first dimension element.
                if ( ! isset( $aField['section_id'] ) || '_default' == $aField['section_id'] ) {
                    return isset( $aOptions[ $aField['field_id'] ] )
                        ? $aOptions[ $aField['field_id'] ]
                        : null;     
                }
                    
                // At this point, the section dimension is set.
                
                // If it belongs to a sub section,
                if ( isset( $aField['_section_index'] ) ) {
                    return isset( $aOptions[ $aField['section_id'] ][ $aField['_section_index'] ][ $aField['field_id'] ] )
                        ? $aOptions[ $aField['section_id'] ][ $aField['_section_index'] ][ $aField['field_id'] ]
                        : null;     
                }
                
                // Otherwise, return the second dimension element.
                return isset( $aOptions[ $aField['section_id'] ][ $aField['field_id'] ] )
                    ? $aOptions[ $aField['section_id'] ][ $aField['field_id'] ]
                    : null;
                                                
            }     
}