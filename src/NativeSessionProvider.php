<?php

namespace EasyCSRF;

use EasyCSRF\Interfaces\SessionProvider;

class NativeSessionProvider implements SessionProvider
{
    /**
     * Get a session value.
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Set a session value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
}
