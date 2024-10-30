<?php

class Listo_Value_Validation {

	public $property;
	public $value;

    public function _($property, $value) {

		$this->property = $property;
		$this->value = $value;

        $sanitize_method = 'sanitize_'.$property;
        $validate_method = 'validate_'.$property;

        if(method_exists($this, $sanitize_method)) {
            $value = $this->$sanitize_method($value);
        }

        if(method_exists($this, $validate_method)) {
            $value = $this->$validate_method($value);
        }

        return $value;
    }

    /** INT - ID **/
    private function sanitize_id($value) {
        return $this->sanitize_int($value);
    }

    private function sanitize_collection($value) {
        $value =  $this->sanitize_int($value);
        // If category is text, we are probably adding to INBOX
        return (is_int($value) && $value > 0) ? $value : NULL;
    }

    private function validate_id($value) {
        return $this->validate_int_positive($value);
    }

    /** INT - ORDER **/
    private function sanitize_order($value) {
        return $this->sanitize_int($value);
    }

    private function validate_order($value) {
        return $this->validate_int_not_negative($value);
    }

    /** INT - SMART **/
    private function sanitize_smart($value) {
        return $this->sanitize_int($value);
    }

    private function validate_smart($value) {
        // validate_bool
        if(in_array($value,[0,1])) {
            return $value;
        }
        return new Listo_Value_Validation_Error($this->property, $this->value, 'Value must be 0 or 1');
    }

    /** STRING - TITLE */

    /** STRING - DESCRIPTION **/





    /** INT - SHARED METHODS **/
    private function sanitize_int($value) {
        return intval($value);
    }

	private function validate_int_positive($value) {
		if(is_numeric($value) && $value > 0) {
			return $value;
		} else {
			return new Listo_Value_Validation_Error($this->property, $this->value, 'Value must be a positive number');
		}
	}

	private function validate_int_not_negative($value) {
		if(is_numeric($value) && $value >= 0) {
			return $value;
		} else {
			return new Listo_Value_Validation_Error($this->property, $this->value, 'Value must be zero or a positive number');
		}
	}

    /** STRING - SHARED METHODS */
    public function validate_string_not_empty($value) {
        if(is_string($value) && strlen($value) > 0) {
            return $value;
        } else {
            return new Listo_Value_Validation_Error($this->property, $this->value, 'Value cannot be empty');
        }
    }
}

class Listo_Value_Validation_Error {

	public $property;
	public $value;
	public $message;
	public function __construct($property, $value, $message) {
		$this->message = $message;
		$this->value = $value;
		$this->property = $property;
	}
}