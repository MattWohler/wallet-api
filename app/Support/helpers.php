<?php declare(strict_types=1);

if (!function_exists('array_unset')) {
    /**
     * remove/unset a list of keys from an array without modifying the original array
     *
     * @param  array  $data
     * @param  array  $keys
     * @return array
     */
    function array_unset(array $data, array $keys): array
    {
        return array_diff_key($data, array_flip($keys));
    }
}

if (!function_exists('array_contain')) {
    /**
     * Determine if an array contains at least one keys
     *
     * @param  array  $data
     * @param  array  $keys
     * @return bool
     */
    function array_contain(array $data, array $keys): bool
    {
        return
            array_first($keys, static function ($key) use ($data) {
                return in_array($key, $data, true);
            }) !== null;
    }
}
