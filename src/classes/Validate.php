<?php
class Validate
{
    private $_passed = false,
        $_errors = array(),
        $_db;

    public function __construct()
    {
        // $this->_db = DatabaseConn::getInstance();
    }

    public function check($source, $items = array(), $extraparam = null)
    {
        try {
            $this->_passed = false;
            foreach ($items as $item => $rules) {
                $itemName = $this->escape('');  // Default itemName, to be overwritten if needed
                foreach ($rules as $rule => $ruleValue) {
                    $value = '';

                    // Check if $source[$item] is set
                    if (isset($source[$item])) {
                        // Handle array case
                        if (is_array($source[$item])) {
                            $value = $source[$item];  // Assign the array as the value
                        } else {
                            $value = trim($source[$item]);  // Otherwise, trim the value
                        }
                    }

                    // Escape the item key
                    $item = $this->escape($item);

                    // If the rule is 'dname', set the itemName
                    if ($rule === 'dname') {
                        $itemName = $ruleValue;
                    }

                    // Check the 'required' rule
                    if ($rule === 'required') {
                        if (is_array($value)) {
                            // Check if the array is empty or has empty elements
                            if (empty($value) || count(array_filter($value)) === 0) {
                                $this->addError("{$itemName} is required");
                            }
                        } else {
                            // Check if the value is an empty string
                            if (empty($value) || $value === 'null' || $value === '') {
                                $this->addError("{$itemName} is required");
                            }
                        }
                    }

                    // Continue with other rules like min, max, etc.
                    if (!empty($value)) {
                        switch ($rule) {
                            case 'min':
                                if (strlen($value) < $ruleValue) {
                                    $this->addError("{$itemName} must be a min of {$ruleValue} chars");
                                }
                                break;
                            case 'max':
                                if (strlen($value) > $ruleValue) {
                                    $this->addError("{$itemName} must be a max of {$ruleValue} chars");
                                }
                                break;
                            case 'notPast':
                                $date = new DateTime($value);
                                $now = new DateTime();
                                if ($date < $now) {
                                    $this->addError("{$itemName} cannot be in the past");
                                }
                                break;
                            case 'isDate':
                                $dateFormat = 'Y-m-d';
                                $dateObject = DateTime::createFromFormat($dateFormat, $value);
                                if (!$dateObject || $dateObject->format($dateFormat) !== $value) {
                                    // Date is invalid
                                    $this->addError("The {$itemName} is not valid.<br>");
                                }
                                break;
                            case 'isTime':
                                $timeFormat = 'H:i:s';
                                $timeObject = DateTime::createFromFormat($timeFormat, $value);
                                if (!$timeObject || $timeObject->format($timeFormat) !== $value) {
                                    // Time is invalid
                                    $this->addError("The {$itemName} is not valid.<br>");
                                }
                                break;
                            case 'isDateTime':
                                $datetimeFormat = 'Y-m-d\TH:i';
                                $datetimeObject = DateTime::createFromFormat($datetimeFormat, $value);
                                if (!$datetimeObject || $datetimeObject->format($datetimeFormat) !== $value) {
                                    $this->addError("The {$itemName} is not valid.<br>");
                                }
                                break;
                            case 'match':
                                if ($value != $source[$ruleValue]) {
                                    $this->addError("{$itemName} should mutch");
                                }
                                break;
                            case 'isMail':
                                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                    $this->addError("The {$itemName} address you provided is not valid.<br>");
                                }
                                break;

                                /*
                             |------------------------------------------------------------------
                             | New Validation Rules
                             |------------------------------------------------------------------
                             */

                                // 1) isString => ensures the value is not purely numeric (or other checks)
                            case 'isString':
                                // If you need to ensure it's purely letters/characters (not numeric):
                                // Option A (simple check: reject if numeric):
                                if (is_numeric($value)) {
                                    $this->addError("{$itemName} must be a valid string, numeric given");
                                }
                                // Option B (regex check) => commented out unless you prefer stricter rules
                                /*
                                if (!preg_match('/^[\p{L}\p{N}\p{P}\p{Z}\p{S}]+$/u', $value)) {
                                    $this->addError("{$itemName} must be a valid string (no invalid chars)");
                                }
                                */
                                break;

                                // 2) in => checks if $value is in an allowed list
                            case 'in':
                                if (is_array($ruleValue)) {
                                    if (!in_array($value, $ruleValue)) {
                                        $allowed = implode(', ', $ruleValue);
                                        $this->addError("{$itemName} must be one of the following: {$allowed}");
                                    }
                                }
                                break;

                                // 3) numeric => checks if $value is numeric
                            case 'numeric':
                                if (!is_numeric($value)) {
                                    $this->addError("{$itemName} must be numeric");
                                }
                                break;

                            default:
                                // If a rule is encountered that we haven't explicitly handled
                                break;
                        }
                    }
                }
            }

            if (empty($this->_errors)) {
                $this->_passed = true;
            }
            return $this;
        } catch (Exception $th) {
            // Handle exception if needed
        }
    }

    public function errors()
    {
        return $this->_errors;
    }

    public function passed()
    {
        return $this->_passed;
    }

    private function addError($error)
    {
        $this->_errors[] = $error;
    }

    function escape($string)
    {
        return htmlentities($string, ENT_QUOTES, 'UTF-8');
    }
}
