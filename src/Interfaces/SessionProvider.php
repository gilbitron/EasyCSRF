<?php namespace EasyCSRF\Interfaces;

interface SessionProvider {

	public function get($key);

	public function set($key, $value);

}