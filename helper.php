<?php

if (!function_exists('cacheable_tag_name')) {
    /**
     * Resolve tag name by model
     *
     * @param string $class_name
     * @return string
     */
    function cacheable_tag_name(string $class_name) : string
    {
        $parts = explode('\\', $class_name);

        return strtolower(end($parts));
    }
}
