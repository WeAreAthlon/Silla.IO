<?php
use Core\Modules\Session\Adapters\Standard;

/**
 * @covers Core\Modules\Session\Adapters\Standard
 */
class StandardTest extends PHPUnit_Framework_TestCase
{
    protected $standardSession;

    protected function setUp()
    {
        $_SERVER['SERVER_NAME'] = 'www.example.com';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/4.5 [en] (X11; U; Linux 2.2.9 i586)';
        $this->standardSession = new Standard();
    }

    /**
     * @covers Core\Modules\Session\Adapters\Standard::__construct
     */
    public function testConstructingStandardSession()
    {
        $this->assertTrue(true);
    }
}
