<?php

use EasyCSRF\EasyCSRF;
use EasyCSRF\NativeSessionProvider;

class EasyCSRFTest extends PHPUnit_Framework_TestCase {

	protected $easyCSRF;

	protected function setUp()
	{
		$_SERVER['REMOTE_ADDR'] = '1.1.1.1';
		$_SERVER['HTTP_USER_AGENT'] = 'useragent';

		$sessionProvider = new NativeSessionProvider();
		$this->easyCSRF = new EasyCSRF($sessionProvider);
	}

	public function testGenerate()
	{
		$token = $this->easyCSRF->generate('test');

		$this->assertNotNull($token);
	}

	public function testCheck()
	{
		$token = $this->easyCSRF->generate('test');
		$this->easyCSRF->check('test', $token);

		$this->assertNull($_SESSION['easycsrf_test']);
	}

	public function testCheckMultiple()
	{
		$token = $this->easyCSRF->generate('test');
		$this->easyCSRF->check('test', $token, null, true);

		$this->assertNotNull($_SESSION['easycsrf_test']);
	}

	public function testExceptionMissingFormToken()
	{
		$this->setExpectedException('Exception', 'Missing CSRF form token.');

		$this->easyCSRF->check('test', '');
	}

	public function testExceptionMissingSessionToken()
	{
		$this->setExpectedException('Exception', 'Missing CSRF session token.');

		$this->easyCSRF->check('test', '12345');
	}

	public function testExceptionOrigin()
	{
		$this->setExpectedException('Exception', 'Form origin does not match token origin.');

		$token = $this->easyCSRF->generate('test');
		$_SERVER['REMOTE_ADDR'] = '2.2.2.2';
		$this->easyCSRF->check('test', $token);
	}

	public function testExceptionInvalidToken()
	{
		$this->setExpectedException('Exception', 'Invalid CSRF token.');

		$this->easyCSRF->generate('test');
		$this->easyCSRF->check('test', '12345');
	}

	public function testExceptionExpired()
	{
		$this->setExpectedException('Exception', 'CSRF token has expired.');

		$token = $this->easyCSRF->generate('test');
		sleep(2);
		$this->easyCSRF->check('test', $token, 1);
	}

}