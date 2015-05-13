<?php
use Core\Helpers\Mailer;
use Eloquent\Phpunit\ParameterizedTestCase;
use \Imagine\Gd\Imagine;
use \Imagine\Image\Box;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * Email testing requires nullmailer and fakemail programs installed and configured.
 *
 * @covers Core\Helpers\Mailer
 */
class MailerTest extends ParameterizedTestCase
{
    protected static $vfs;
    protected static $rootPath;
    protected $imagePath;
    protected $width;
    protected $height;
    protected $params;
    protected $emailsPath;
    protected $auth;

    public static function setUpBeforeClass()
    {
        /* Setup virtual file system. */
        self::$vfs = vfsStream::setup('root/');

        self::$rootPath = Core\Config()->paths('root');
        /* Modify root path to point to the virtual file system. */
        Core\Config()->modifyPath('root', vfsStream::url('root/'));
    }

    public static function tearDownAfterClass()
    {
        /* Tear down virtual file system. */
        self::$vfs = null;
        /* Reset root path to point to the real file system. */
        Core\Config()->modifyPath('root', self::$rootPath);
    }

    public function getTestCaseParameters()
    {
        return array(
            array('Sendmail'),
            array('SMTP')
        );
    }

    public function setUpParameterized($serverType)
    {
        Core\Config()->MAILER['type'] = $serverType;
    }

    protected function setUp()
    {
        $this->imagePath = Core\Config()->paths('root') . 'image.png';
        $this->width = 640;
        $this->height = 480;

        $imagine = new Imagine();
        $imagine
            ->create(new Box($this->width, $this->height))
            ->save($this->imagePath);

        $this->params = array(
            'from' => array(
                Core\Config()->MAILER['identity']['email'] => Core\Config()->MAILER['identity']['name']
            ),
            'to' => array(
                'aleksandar.krastev@athlonsofia.com' => 'Aleksandar'
            ),
            'reply_to' => array(
                'plamen@athlonsofia.com' => 'Plamen'
            ),
            'custom_headers' => array(
                'X-Company' => 'Athlon'
            ),
            'subject' => 'Testing',
            'content' => 'test',
            'inline_attachments' => array(
                $this->imagePath => md5($this->imagePath)
            )
        );
        $this->emailsPath = '/tmp/emails';
        $this->auth = false;
    }

    private function getSentEmail()
    {
        /* Send email with supplied parameters. */
        Mailer::send($this->params, $this->auth);
        /* Wait a second for email interception. */
        sleep(1);
        /* Get contents of email from file. */
        return file_get_contents($this->getLastMessage());
    }

    private function getLastMessage()
    {
        $emails = array();
        foreach (array_slice(scandir($this->emailsPath), 2) as $email) {
            $emailPath = $this->emailsPath . DIRECTORY_SEPARATOR . $email;
            $emails[$emailPath] = filemtime($emailPath);
        }
        arsort($emails);
        reset($emails);
        return key($emails);
    }

    /**
     * @covers Core\Helpers\Mailer::send
     */
    public function testSendingEmailSuccessfully()
    {
        $this->assertTrue(Mailer::send($this->params, $this->auth));
    }

    /**
     * @covers Core\Helpers\Mailer::send
     * @expectedException phpmailerException
     */
    public function testSendingEmailWithoutARecipient()
    {
        /* Remove recipient email address. */
        unset($this->params['to']);

        $this->assertFalse(Mailer::send($this->params, $this->auth));
    }

    /**
     * @covers Core\Helpers\Mailer::send
     */
    public function testHeaderFieldsMatch()
    {
        /* Send email and retrieve message. */
        $email = $this->getSentEmail();

        /* Find standard header fields. */
        preg_match('/To: (.+) <(.+)>/', $email, $to);
        preg_match('/From: (.+) <(.+)>/', $email, $from);
        preg_match('/Subject: (.+)\r\n/', $email, $subject);

        /* Assert that sender email address and name match. */
        $this->assertEquals(current($this->params['from']), $from[1]);
        $this->assertEquals(key($this->params['from']), $from[2]);

        /* Assert that recipient email address and name match. */
        $this->assertEquals(current($this->params['to']), $to[1]);
        $this->assertEquals(key($this->params['to']), $to[2]);

        /* Assertion for subject. */
        $this->assertEquals($this->params['subject'], $subject[1]);
    }

    /**
     * @covers Core\Helpers\Mailer::send
     */
    public function testSendingEmailToMultipleRecipients()
    {
        /* Remove recipient email address. */
        unset($this->params['to']);
        /* Add multiple recipient email addresses. */
        $this->params['to'] = array(
            'plamen@athlonsofia.com' => 'Plamen',
            'kalin@athlonsofia.com' => 'Kalin'
        );

        /* Send email and retrieve message. */
        $email = $this->getSentEmail();

        /* Find To header field. */
        preg_match('/To: (.+) <(.+)>, (.+) <(.+)>/', $email, $to);

        /* Assertion that recipients email addresses and names match. */
        $this->assertEquals(current($this->params['to']), $to[1]);
        $this->assertEquals(key($this->params['to']), $to[2]);
        $this->assertEquals(next($this->params['to']), $to[3]);
        $this->assertEquals(key($this->params['to']), $to[4]);
    }

    /**
     * @covers Core\Helpers\Mailer::send
     */
    public function testReplyToHeaderFieldMatches()
    {
        /* Send email and retrieve message. */
        $email = $this->getSentEmail();

        /* Find Reply-To header field. */
        preg_match('/Reply-To: (.+) <(.+)>/', $email, $replyTo);

        /* Assertion that reply-to email address and name matches. */
        $this->assertEquals(current($this->params['reply_to']), $replyTo[1]);
        $this->assertEquals(key($this->params['reply_to']), $replyTo[2]);
    }

    /**
     * @covers Core\Helpers\Mailer::send
     */
    public function testAddingInlineAttachment()
    {
        /* Send email and retrieve message. */
        $email = $this->getSentEmail();

        /* Find inline attachment header fields. */
        preg_match('/Content-ID: <(.+)>/', $email, $contentId);
        preg_match('/Content-Disposition: (.+); filename=(.+)\r\n/', $email, $contentDisp);

        /* Assert that content ID is equal. */
        $this->assertEquals(current($this->params['inline_attachments']), $contentId[1]);
        /* Assert that attachment is of inline type. */
        $this->assertEquals('inline', $contentDisp[1]);
        /* Assert that filename of attachment matches. */
        $this->assertEquals(basename(key($this->params['inline_attachments'])), $contentDisp[2]);
    }

    /**
     * @covers Core\Helpers\Mailer::send
     */
    public function testCustomHeaderFieldMatch()
    {
        /* Send email and retrieve message. */
        $email = $this->getSentEmail();

        /* Find custom header field. */
        preg_match_all('/(X-.+): (.+)\r\n/', $email, $customHeaders);

        /* Assert that custom header name and value match. */
        $this->assertContains(key($this->params['custom_headers']), $customHeaders[1]);
        $this->assertContains(current($this->params['custom_headers']), $customHeaders[2]);
    }
}
