<?php

if (!function_exists('set_active')) {
    function set_active($path, $active = 'active')
    {
        return call_user_func_array('Request::is', (array)$path) ? $active : '';
    }
}
