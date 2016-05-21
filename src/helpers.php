<?php namespace Nine\Library;

/**
 * Globally accessible convenience functions.
 *
 * @package Nine Collections
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */

use Closure;
use Nine\Collections\Collection;

if (PHP_VERSION_ID < 70000) {
    echo('Formula 9 requires PHP versions >= 7.0.0');
    exit(1);
}

if (defined('HELPERS_LOADED')) {
    return TRUE;
}

define('HELPERS_LOADED', TRUE);

if ( ! function_exists('array_accept')) {
    /**
     * Get all of the given array except for a specified array of items.
     *
     * @param  array|string $keys
     * @param  array        $array
     *
     * @return array
     */
    function array_except($array, $keys)
    {
        return array_diff_key($array, array_flip((array) $keys));
    }
}

if ( ! function_exists('collect')) {
    /**
     * Returns a collection containing the array values provided.
     *
     * @param array $array
     *
     * @return Collection
     */
    function collect(array $array)
    {
        return new Collection($array);
    }
}

if ( ! function_exists('e')) {
    /**
     * Escape HTML entities in a string.
     *
     * @param  string $value
     *
     * @return string
     */
    function e($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', FALSE);
    }
}

if ( ! function_exists('elapsed_time_since_request')) {
    /**
     * @param bool $raw
     *
     * @return string
     */
    function elapsed_time_since_request($raw = FALSE)
    {
        return ! $raw
            ? sprintf('%8.1f ms', (microtime(TRUE) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000)
            : (microtime(TRUE) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000;
    }
}

if ( ! function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    function env($key, $default = NULL)
    {
        $value = getenv($key);

        if ($value === FALSE) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return TRUE;

            case 'false':
            case '(false)':
                return FALSE;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return NULL;
        }

        if (strlen($value) > 1 && Lib::starts_with('"', $value) && Lib::ends_with('"', $value)) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if ( ! function_exists('pad_left')) {

    /**
     * Left-pad a string
     *
     * @param string $str
     * @param int    $length
     * @param string $space
     *
     * @return string
     */
    function pad_left($str, $length = 0, $space = ' ')
    {
        return str_pad($str, $length, $space, STR_PAD_LEFT);
    }
}

if ( ! function_exists('pad_right')) {

    /**
     * Left-pad a string
     *
     * @param string $str
     * @param int    $length
     * @param string $space
     *
     * @return string
     */
    function pad_right($str, $length = 0, $space = ' ')
    {
        return str_pad($str, $length, $space, STR_PAD_RIGHT);
    }
}

if ( ! function_exists('memoize')) {
    /**
     * Cache repeated function results.
     *
     * @param $lambda - the function whose results we cache.
     *
     * @return Closure
     */
    function memoize($lambda)
    {
        return function () use ($lambda) {
            # results cache
            static $results = [];

            # collect arguments and serialize the key
            $args = func_get_args();
            $key = serialize($args);

            # if the key result is not cached then cache it
            if (empty($results[$key])) {
                $results[$key] = call_user_func_array($lambda, $args);
            }

            return $results[$key];
        };
    }
}

if ( ! function_exists('is_not')) {

    function is_not($subject)
    {
        return ! $subject;
    }
}

if ( ! function_exists('partial')) {
    /**
     * Curry a function.
     *
     * @param $lambda - the function to curry.
     * @param $arg    - the first or only argument
     *
     * @return Closure
     */
    function partial($lambda, $arg)
    {
        $func_args = func_get_args();
        $args = array_slice($func_args, 1);

        return function () use ($lambda, $args) {
            $full_args = array_merge($args, func_get_args());

            return call_user_func_array($lambda, $full_args);
        };
    }
}

if ( ! function_exists('value')) {
    /**
     *  Returns value of a variable. Resolves closures.
     *
     * @param  mixed $value
     *
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if ( ! function_exists('throw_now')) {

    /**
     * @param $exception
     * @param $message
     *
     * @return null
     */
    function throw_now($exception, $message)
    {
        throw new $exception($message);
    }
}

if ( ! function_exists('throw_if')) {
    /**
     * @param string  $exception
     * @param string  $message
     * @param boolean $if
     */
    function throw_if($if, $exception, $message)
    {
        if ($if) {
            throw new $exception($message);
        }
    }
}

if ( ! function_exists('throw_if_not')) {
    /**
     * @param string  $exception
     * @param string  $message
     * @param boolean $if
     */
    function throw_if_not($if, $exception, $message)
    {
        if ( ! $if) {
            throw new $exception($message);
        }
    }
}

if ( ! function_exists('tail')) {
    // blatantly stolen from Ionuț G. Stan on stack overflow
    function tail($filename)
    {
        $line = '';

        $f = fopen(realpath($filename), 'r');
        $cursor = -1;

        fseek($f, $cursor, SEEK_END);
        $char = fgetc($f);

        /**
         * Trim trailing newline chars of the file
         */
        while ($char === "\n" || $char === "\r") {
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        /**
         * Read until the start of file or first newline char
         */
        while ($char !== FALSE && $char !== "\n" && $char !== "\r") {
            /**
             * Prepend the new char
             */
            $line = $char . $line;
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        return $line;
    }
}

if ( ! function_exists('dd')) {

    /**
     * Override Illuminate dd()
     *
     * @param null $value
     * @param int  $depth
     */
    function dd($value = NULL, $depth = 8)
    {
        ddump($value, $depth);
    }
}

if ( ! function_exists('w')) {

    /**
     * Converts a string of space or tab delimited words as an array.
     * Multiple whitespace between words is converted to a single space.
     *
     * ie:
     *      w('one two three') -> ['one','two','three']
     *      w('one:two',':') -> ['one','two']
     *
     *
     * @param string $words
     * @param string $delimiter
     *
     * @return array
     */
    function w($words, $delimiter = ' ')
    {
        return explode($delimiter, preg_replace('/\s+/', ' ', $words));
    }
}

if ( ! function_exists('ww')) {

    /**
     * Converts an encoded string to an associative array.
     *
     * ie:
     *      tuples('one:1, two:2, three:3') -> ["one" => 1,"two" => 2,"three" => 3,]
     *
     * @param $encoded_string
     *
     * @return array
     */
    function tuples($encoded_string)
    {
        $array = w($encoded_string, ',');
        $result = [];

        foreach ($array as $tuple) {
            $ra = explode(':', $tuple);

            $key = trim($ra[0]);
            $value = trim($ra[1]);

            $result[$key] = is_numeric($value) ? (int) $value : $value;
        }

        return $result;
    }
}
