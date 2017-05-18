<?php
/**
 * Authentication Controller.
 *
 * @package    Silla.IO
 * @subpackage CMS\Controllers
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace CMS\Controllers;

use Core;
use Core\Base;
use Core\Modules\Crypt\Crypt;
use Core\Modules\Router\Request;
use CMS\Models;
use CMS\Helpers;

/**
 * Class Authentication Controller definition.
 */
class Authentication extends Base\Controller
{
    /**
     * Layout name.
     *
     * @var string
     */
    public $layout = 'public';

    /**
     * Captcha feature.
     *
     * @var \Captcha\Captcha
     */
    public $captcha;

    /**
     * @var array
     */
    public $errors = array();

    /**
     * Authentication constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->addBeforeFilters(array('loadAssets'));
        $this->addAfterFilters(array('loadFlashMessage'));

        if (Core\Config()->CAPTCHA['enabled'] && in_array(Core\Router()->request->action(), array('login', 'reset'))) {
            $this->loadCaptcha(Core\Config()->CAPTCHA);
        }
    }

    /**
     * Login action.
     *
     * Updates the user login time.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    public function login(Request $request)
    {
        if ($request->is('post')) {
            if ($this->captcha) {
                if (!Helpers\Captcha::isValid($this->captcha)) {
                    Helpers\FlashMessage::set($this->labels['captcha']['error'], 'danger');

                    return;
                }
            }

            $user = Models\CMSUser::find()->where('email = ?', array($request->post('email')))->first();

            if ($user && Crypt::hashCompare($user->password, $request->post('password'))) {
                $user->save(array('login_on' => gmdate('Y-m-d H:i:s')), true);

                /* Regenerate Session key for prevent session id fixation. */
                Core\Session()->regenerateKey();
                Core\Session()->set('cms_user_info', rawurlencode(serialize($user)));
                Core\Session()->set('cms_user_logged', 1);
                Core\Session()->remove('authentication_error');
                Core\Session()->remove('captcha');

                /* Regenerate CSRF token for prevent token fixation. */
                Core\Session()->remove('_token');
                $request->regenerateToken();

                if ($request->get('redirect')) {
                    $request->redirectTo($request->get('redirect'));
                } else {
                    $request->redirectTo(array('controller' => 'account'));
                }
            } else {
                Helpers\FlashMessage::set($this->labels['login']['error'], 'danger');
                Core\Session()->set('authentication_error', true);

                if (Core\Config()->CAPTCHA['enabled']) {
                    $this->loadCaptcha(Core\Config()->CAPTCHA);
                }
            }
        } else {
            if (Core\Session()->get('cms_user_logged') === 1) {
                $request->redirectTo(array('controller' => 'account'));
            }
        }
    }

    /**
     * Logout action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    public function logout(Request $request)
    {
        Core\Session()->destroy();

        $request->redirectTo('login');
    }

    /**
     * Password reset action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    public function reset(Request $request)
    {
        if ($request->is('post')) {
            $this->errors = array();
            $user         = new Models\CMSUser;
            $email        = $request->post('email');

            if ($this->captcha && !Helpers\Captcha::isValid($this->captcha)) {
                $this->errors['captcha'] = true;
            } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $this->errors['email'] = true;
            } elseif (!($user = Models\CMSUser::find()->where('email = ?', array($email))->first())) {
                $this->errors['email'] = true;
            }

            if (!$this->errors) {
                $user->save(array('updated_on' => gmdate('Y-m-d H:i:s')), true);

                $this->renderer->set('name', $user->name);
                $this->renderer->set('password_reset_link', Core\Router()->toFullUrl(array(
                    'controller' => 'authentication',
                    'action'     => 'renew',
                    'id'         => sha1($user->password . Core\Config()->USER_AUTH['cookie_salt'] . $user->email),
                )));

                $mailForPasswordReset = array(
                    'from'    => array(
                        Core\Config()->MAILER['identity']['email'] => Core\Config()->MAILER['identity']['name'],
                    ),
                    'to'      => array(
                        $user->email => $user->name,
                    ),
                    'subject' => $this->labels['mails']['reset']['subject'],
                    'content' => $this->getPartialOutput('authentication/mails/password_reset'),
                );

                Core\Helpers\Mailer::send($mailForPasswordReset);
                Helpers\FlashMessage::set($this->labels['reset']['success'], 'success');
                Core\Session()->remove('authentication_error');
                Core\Session()->remove('captcha');
            } else {
                if ($this->captcha) {
                    Helpers\FlashMessage::set($this->labels['captcha']['error'], 'danger');
                } else {
                    Helpers\FlashMessage::set($this->labels['reset']['error'], 'danger');
                }

                Core\Session()->set('authentication_error', true);

                if (Core\Config()->CAPTCHA['enabled']) {
                    $this->loadCaptcha(Core\Config()->CAPTCHA);
                }
            }
        }
    }

    /**
     * Reset access action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    public function renew(Request $request)
    {
        $user = Models\CMSUser::find()->where(
            'DATE_ADD(updated_on, INTERVAL 60 MINUTE) > UTC_TIMESTAMP() AND SHA1(CONCAT(password, ?, email)) = ?',
            array(
                Core\Config()->USER_AUTH['cookie_salt'],
                $request->get('id'),
            )
        )->first();

        if ($user) {
            $new_password = Core\Utils::generatePassword(10);

            if ($user->save(array('password' => $new_password), true)) {
                $this->new_password = $new_password;
            }
        } else {
            $request->redirectTo(array('controller' => 'authentication'));
        }
    }

    /**
     * Loads the CAPTCHA.
     *
     * @param array $configuration Captcha Configuration data.
     *
     * @return void
     */
    private function loadCaptcha(array $configuration)
    {
        $this->captcha = Helpers\Captcha::get($configuration);

        if ($this->captcha) {
            $this->renderer->set('captchaTemplate', Helpers\Captcha::getTemplate($this->captcha));
        }
    }

    /**
     * Loads Login Assets.
     *
     * @return void
     */
    protected function loadAssets()
    {
        $this->renderer->assets->add(array(
            'vendor/components/jquery/jquery.js',
            'vendor/components/bootstrap/js/bootstrap.js',
            'vendor/components/bootstrap/css/bootstrap.css',
            'cms/assets/css/bootstrap-theme.silla.css',
            'cms/assets/css/login.silla.css',
        ));
    }

    /**
     * Loads Flash Messages.
     *
     * @return void
     */
    protected function loadFlashMessage()
    {
        $this->renderer->set('flash', Helpers\FlashMessage::get());
    }
}
