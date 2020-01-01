<?php
declare(strict_types=1);

namespace EasyCSRF;

use EasyCSRF\Exceptions\{
    ExpiredTokenException, MissingSessionTokenException, MissingTokenException, TokenMismatchException,
};

use EasyCSRF\Interfaces\SessionProvider;

class EasyCSRF
{

    protected $session;

    protected $session_prefix = 'easycsrf_';

    public function __construct(SessionProvider $sessionProvider)
    {
        $this->session = $sessionProvider;
    }

    /**
     * Generate a CSRF token
     *
     * @param string $key Key for this token
     * @return string
     * @throws \Exception
     */
    public function generate($key): string
    {
        $key = $result = preg_replace('/[^a-zA-Z0-9]+/', '', $key);

        $extra = sha1($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
        // time() is used for token expiration
        $token = base64_encode(time() . $extra . $this->randomString(32));
        $this->session->set($this->session_prefix . $key, $token);

        return $token;
    }

    /**
     * Check the CSRF token is valid
     *
     * @param string $key Key for this token
     * @param string $token The token string (usually found in $_POST)
     * @param int $timespan Makes the token expire after $timespan seconds (null = never)
     * @param boolean $multiple Makes the token reusable and not one-time (Useful for ajax-heavy requests)
     * @throws ExpiredTokenException
     * @throws MissingSessionTokenException
     * @throws MissingTokenException
     * @throws TokenMismatchException
     */
    public function check($key, $token, $timespan = null, $multiple = false): void
    {
        $key = $result = preg_replace('/[^a-zA-Z0-9]+/', '', $key);

        if (!$token) {
            throw new MissingTokenException('Missing CSRF form token.');
        }

        $session_token = $this->session->get($this->session_prefix . $key);
        if (!$session_token) {
            throw new MissingSessionTokenException('Missing CSRF session token.');
        }

        if (!$multiple) {
            $this->session->set($this->session_prefix . $key, null);
        }

        if ($this->tokenMisMatches($session_token)) {
            throw new TokenMismatchException('Form origin does not match token origin.');
        }

        if ($token != $session_token) {
            throw new TokenMismatchException('Invalid CSRF token.');
        }

        // Check for token expiration
        if ($this->tokenIsExpired($timespan, $session_token)) {
            throw new ExpiredTokenException('CSRF token has expired.');
        }
    }

    /**
     * Generate a random string
     *
     * @param int $length
     * @return string
     * @throws \Exception
     */
    protected function randomString($length): string
    {
        $seed = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijqlmnopqrtsuvwxyz0123456789';
        $max = strlen($seed) - 1;
        $string = '';
        for ($i = 0; $i < $length; ++$i) {
            $string .= $seed{(int)random_int(0, $max)};
        }

        return $string;
    }

    /**
     * @param $session_token
     * @return bool
     */
    protected function tokenMisMatches($session_token): bool
    {
        return sha1($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']) != substr(base64_decode($session_token), 10, 40);
    }

    /**
     * @param $timespan
     * @param $session_token
     * @return bool
     */
    protected function tokenIsExpired($timespan, $session_token): bool
    {
        return $timespan != null && is_int($timespan) && (int)substr(base64_decode($session_token), 0, 10) + $timespan < time();
    }

}