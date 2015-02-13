<?php namespace EasyCSRF;

use EasyCSRF\Interfaces\SessionProvider;

class NativeSessionProvider implements SessionProvider {

	public function get($key)
	{
		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		}

		return null;
	}

	public function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}

}