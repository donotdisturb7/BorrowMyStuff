<?php

namespace App\Helper;

abstract class Rule {
    protected $message;
    abstract function check($value): bool;
    public function getMessage(): string {
        return $this->message;
    }
}

class Required extends Rule {
    protected $message = "The :attribute is required";
    public function check($value): bool {
        if (is_array($value)) return sizeof($value) <= 0;  // Fixed: changed < to <=
        return empty($value);
    }
}

class Alpha extends Rule {
    protected $message = "The :attribute only allows alphabet";
    public function check($value): bool {
        return !preg_match('/^[\pL\pM]+(?:\s[\pL\pM]+)*$/u', $value);
    }
}

class Email extends Rule {
    protected $message = "The :attribute is not valid email";
    public function check($value): bool {
        return !filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}

// NEW CLASS: Min rule to validate minimum length or value
class Min extends Rule {
    protected $message = "The :attribute must be at least :min characters";
    private $min;
    
    public function __construct($min) {
        $this->min = $min;
        $this->message = str_replace(':min', $min, $this->message);
    }
    
    public function check($value): bool {
        if (is_numeric($value)) {
            return $value < $this->min;
        }
        return strlen($value) < $this->min;
    }
}

class Birthdate extends Rule {
    protected $message = 'The :attribute must be 18+';

    public function check($value): bool {
        if (!preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/', $value)) {
            return true;
        }

        list($year, $month, $day) = explode('-', $value);

        if (!checkdate($month, $day, $year)) {
            return true;
        }

        $birthdate = new \DateTime($value);
        $today = new \DateTime();
        $age = $today->diff($birthdate)->y;

        if ($age >= 18 && $age <= 150) {
            return false;
        }
        return true;
    }
}

class Gender extends Rule {
    protected $message = 'The :attribute is invalid';
    public function check($value): bool {
        return !in_array($value, ['man', 'women']);
    }
}

class Password extends Rule {
    protected $message = 'The :attribute is invalid';
    public function check($value): bool {
        return !preg_match('/^[a-zA-Z0-9]+$/', $value);
    }
}

class Same extends Rule {
    protected $message = "Passwords don't match.";
    public function check($value): bool {
        return $value != $_POST['password'];
    }
}

class Validator {
    private $errors = [];
    private $aliases = [];
    private $data;
    private $rules;

    public function setAliases($aliases) {
        $this->aliases = $aliases;
    }

    public function make($data, $rules) {
        $this->data = $data;
        $this->rules = $rules;
        $this->sanitize($this->data);
        $this->validateField($this->rules);
    }

    public function sanitize(&$data) {
        foreach ($data as $key => $value) {
            if (!is_array($value)) $data[$key] = htmlspecialchars(trim($value));
        }
    }

    public function validateField($rules) {
        foreach ($rules as $field => $rules) {
            $rules = explode('|', $rules);

            foreach ($rules as $rule) {
                // if (!empty($this->errors))  return; // this for one error return from the function
                if (isset($this->errors[$field])) break; // this for all erros but one for each input
                $this->applyRules($field, $rule);
            }
        }
    }

    public function applyRules($field, $rule) {
        $value = $this->data[$field] ?? '';
        
        // Check if the rule has parameters (like min:6)
        if (strpos($rule, ':') !== false) {
            list($ruleName, $parameter) = explode(':', $rule, 2);
            $ruleClassName = 'App\\Helper\\' . ucfirst($ruleName);
            
            if (class_exists($ruleClassName)) {
                $ruleObj = new $ruleClassName($parameter);
                if ($ruleObj->check($value)) {
                    $this->addError($field, $ruleObj->getMessage());
                }
            } else {
                $this->addError($field, "Validation rule '$ruleName' not found");
            }
        } else {
            // Handle rules without parameters
            $ruleClassName = 'App\\Helper\\' . ucfirst($rule);
            
            if (class_exists($ruleClassName)) {
                $ruleObj = new $ruleClassName();
                if ($ruleObj->check($value)) {
                    $this->addError($field, $ruleObj->getMessage());
                }
            } else {
                $this->addError($field, "Validation rule '$rule' not found");
            }
        }
    }

    public function addError($field, $message) {
        $this->errors[$field][] = str_replace(':attribute', $this->aliases[$field] ?? $field, $message);
    }

    public function fails() {
        return !empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }
}