<?php

// application/helpers/env_helper.php
/**
 * .env 파일을 쉽게 쓸 수 있게 해줌
 * @param mixed $key
 * @param mixed $default
 * 
 * ex)
 * $CI = &get_instance();
 * $CI->load->helper('env_helper');
 * $api_key = env('PUBLIC_DATA_COMPANY_NUMBER_API');
 * 
 */
function env($key, $default = null)
{
    static $env = null;
    if ($env === null) {
        $lines = file(APPPATH . '../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            [$k, $v] = explode('=', $line, 2);
            $env[trim($k)] = trim($v);
        }
    }
    return $env[$key] ?? $default;
}
