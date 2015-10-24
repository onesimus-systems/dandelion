<?php
/**
 * Validator is a simple library for validating data.
 */
namespace Dandelion2;

class Validator
{
    private function __construct() { }
    private function __clone() { }

    /**
     * Validate data as a string. Really, just return it type casted as a string
     * @param  mixed $d
     * @return string | null
     */
    protected static function validateString($d)
    {
        return (is_object($d) || is_null($d)) ? null : (string) $d;
    }

    /**
     * Validate data is an object
     * @param  mixed $obj
     * @param  array $options If key 'class' is set, checks if $obj is an instance of the class. Otherwise, simply return is_object.
     * @return bool
     */
    protected static function validateObject($obj, array $options)
    {
        if (isset($options['class'])) {
            if ($obj instanceof $options['class']) {
                return $obj;
            } else {
                return null;
            }
        } elseif (is_object($obj)) {
            return $obj;
        }

        return null;
    }

    /**
     * Generalized validation function
     * @param  mixed $data
     * @param  string $type 'string'|'int'|'float'|'bool'|'email'|'url'|'ip'|'object'
     * @param  array $options Options for validation. Options to pass to the filter_var function should be keyed 'filter_opts'.
     * @return mixed
     */
    public static function validate($data, $type = 'string', array $options = [])
    {
        switch ($type) {
        case 'string':
            return self::validateString($data);
            break;
        case 'int':
            $filterConst = FILTER_VALIDATE_INT;
            break;
        case 'float':
            $filterConst = FILTER_VALIDATE_FLOAT;
            break;
        case 'bool':
            $filterConst = FILTER_VALIDATE_BOOLEAN;
            break;
        case 'email':
            $filterConst = FILTER_VALIDATE_EMAIL;
            break;
        case 'url':
            $filterConst = FILTER_VALIDATE_URL;
            break;
        case 'ip':
            $filterConst = FILTER_VALIDATE_IP;
            break;
        case 'object':
            return self::validateObject($data, $options);
            break;
        default:
            throw \InvalidArgumentException('Type must be string, int, bool, float, object, email, ip, or url');
        }

        $filterOptions = isset($options['filter_opts']) ? $options['filter_opts'] : [];
        if (isset($filterOptions['flags'])) {
            $filterOptions['flags'] = $filterOptions['flags'] | FILTER_NULL_ON_FAILURE;
        } else {
            $filterOptions['flags'] = FILTER_NULL_ON_FAILURE;
        }

        return filter_var($data, $filterConst, $filterOptions);
    }

    /**
     * Validate a batch of values
     * @param  array  $data Arrays of [$val, $type, $options]
     * @return array The indices match the incoming array.
     */
    public static function validateBatch(array $data)
    {
        $return = [];
        foreach ($data as $val) {
            $d = isset($val[0]) ? $val[0] : null;
            $t = isset($val[1]) ? $val[1] : 'string';
            $o = isset($val[2]) ? $val[2] : [];
            $return[] = self::validate($d, $t, $o);
        }
        return $return;
    }

    /**
     * Validate a batch of variables and return a standard class with properties matching the array keys.
     *
     * The incoming data is an array of keyed items in the format:
     * 'var_id' => [$data, $type, $priority, array $options]. $priority is one of the following:
     *  - 'optional' - (Default) Value may fail validation and be set to null.
     *  - 'required' - Value must be not null. Empty passes.
     *
     * $type defaults to 'string'
     * $priority defaults to 'optional'
     * $options defaults to []
     *
     * The return StdClass has properties named with the array keys. The properties hold the validated data as it's respective type.
     * There are two special properties, _valid and _invalidFields. _valid will be set true if all requirements are met. It will be false otherwise.
     * _invalidFields will be an array with all the keys that made the result invalid.
     *
     * @param array $data
     * @return StdClass
     */
    public static function validateBatchKeys(array $data)
    {
        $return = new \StdClass();
        $return->_valid = true;
        $return->_invalidFields = [];

        foreach ($data as $id => $var) {
            $d = isset($var[0]) ? $var[0] : null;
            $t = isset($var[1]) ? $var[1] : 'string';
            $p = isset($var[2]) ? $var[2] : 'optional';
            $o = isset($var[3]) ? $var[3] : [];

            if ($p === 'required' && is_null($d)) {
                $return->_valid = false;
                $return->_invalidFields[] = $id;
                continue;
            }

            $validated = self::validate($d, $t, $o);
            if (is_null($validated) && $p !== 'optional') {
                $return->_valid = false;
                $return->_invalidFields[] = $id;
            }
            $return->$id = $validated;
        }

        return $return;
    }
}
