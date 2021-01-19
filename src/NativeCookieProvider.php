<?php

namespace EasyCSRF;

use EasyCSRF\Interfaces\SessionProvider;

class NativeCookieProvider implements SessionProvider
{
    /**
     * Get a cookie value.
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $_COOKIE[$key] ?? null;
    }

    /**
     * Set a cookie value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $_COOKIE[$key] = $value;
    }
}
