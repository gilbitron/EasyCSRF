<?php

namespace EasyCSRF\Interfaces;

interface SessionProvider
{
    /**
     * Get a session value.
     *
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * Set a session value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value);
}
