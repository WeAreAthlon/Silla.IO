<?php
/**
 * CMS Controller.
 *
 * @package    Silla
 * @subpackage CMS\Controllers
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace CMS\Controllers;

use Core;
use Core\Modules\Crypt\Crypt;
use Core\Modules\Router\Request;
use CMS\Models;
use CMS\Helpers;
use \Captcha;

/**
 * Class CMS Controller definition.
 */
class CMS extends Core\Base\Resource
{
    /**
     * Skips ACL generations for the listed methods.
     *
     * @var array
     */
    public $skipAclFor = array();

    /**
     * Loaded CMS modules.
     *
     * @var array
     */
    public $modules = array();

    /**
     * Currently logged user instance.
     *
     * @var \CMS\Models\CMSUser
     */
    protected $user;

    /**
     * Init method.
     */
    public function __construct()
    {
        parent::__construct();

        $this->addBeforeFilters(array('checkLogged'), array(
            'except' => array('login', 'reset', 'renew')
        ));

        $this->addBeforeFilters(array('checkPermissions'), array(
            'except' => array_merge(array('login', 'logout', 'reset', 'renew'), $this->skipAclFor)
        ));

        $this->addBeforeFilters(array('loadVendorAssets'));

        $this->addBeforeFilters(array('loadFormAssets'), array(
            'only' => array('create', 'edit')
        ));

        $this->addBeforeFilters(array('loadListingAssets'), array(
            'only' => array('index')
        ));

        $this->addAfterFilters(array('loadAccessibilityScope', 'loadCmsAssets'), array(
            'except' => array('login', 'reset', 'renew')
        ));
    }

    /**
     * Login action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    public function login(Request $request)
    {
        $this->renderer->setLayout('public');
        $this->renderer->assets->remove('css/style.css');
        $this->renderer->assets->add('css/login.css');

        if (Core\Session()->get('login_error')) {
            $this->captcha = Core\Session()->get('captcha')->html();
        }

        if ($request->is('post')) {
            if (Core\Session()->get('login_error') && !Core\Session()->get('captcha')->check()->isValid()) {
                $labelsCaptcha = Core\Helpers\YAML::get('captcha', $this->labels);
                Helpers\FlashMessage::setMessage($labelsCaptcha['error'], 'danger');
                $this->captcha = Core\Session()->get('captcha')->html();

                return;
            }

            $user = Models\CMSUser::find()->where('email = ?', array($request->post('email')))->first();

            if ($user && Crypt::hashCompare($user->password, $request->post('password'))) {
                /* Update the user login time */
                $user->save(array('login_on' => gmdate('Y-m-d H:i:s')), true);

                /* Regenerate Session key for prevent session fixation */
                Core\Session()->regenerateKey();

                Core\Session()->set('user_info', rawurlencode(serialize($user)));
                Core\Session()->set('user_logged', 1);
                Core\Session()->remove('login_error');

                /* Regenrate CSRF token for prevent fixation */
                Core\Session()->remove('_token');
                $request->regenerateToken();

                if ($request->get('redirect')) {
                    $request->redirectTo(Core\Config()->urls('full') . $request->get('redirect'));
                } else {
                    $request->redirectTo(array('controller' => 'users', 'action' => 'account'));
                }
            } else {
                $labels_login = Core\Helpers\YAML::get('login', $this->labels);
                Helpers\FlashMessage::setMessage($labels_login['error'], 'danger');

                $captcha = new Captcha\Captcha();
                $captcha->setPublicKey(Core\Config()->CAPTCHA['public_key']);
                $captcha->setPrivateKey(Core\Config()->CAPTCHA['private_key']);

                Core\Session()->set('login_error', true);
                Core\Session()->set('captcha', $captcha);

                $this->captcha = $captcha->html();
            }
        } else {
            if (Core\Session()->get('user_logged') === 1) {
                $request->redirectTo(array('controller' => 'users', 'action' => 'account'));
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
        $this->renderer->setLayout('public');
        $this->renderer->assets->remove('css/style.css');
        $this->renderer->assets->add('css/login.css');

        if (Core\Session()->get('password_reset_error')) {
            $this->captcha = Core\Session()->get('captcha')->html();
        }

        if ($request->is('post')) {
            $labelsReset   = Core\Helpers\YAML::get('reset', $this->labels);
            $labelsCaptcha = Core\Helpers\YAML::get('captcha', $this->labels);

            $user = new Models\CMSUser;

            $this->errors = array();

            if (Core\Session()->get('password_reset_error') && !Core\Session()->get('captcha')->check()->isValid()) {
                $this->errors['captcha'] = true;
            } elseif (filter_var($request->post('email'), FILTER_VALIDATE_EMAIL) === false) {
                $this->errors['email'] = true;
            } elseif (!($user = Models\CMSUser::find()->where('email = ?', array($request->post('email')))->first())) {
                $this->errors['email'] = true;
            }

            if (!$this->errors) {
                $user->save(array('updated_on' => gmdate('Y-m-d H:i:s')), true);

                $this->name = $user->name;
                $this->password_reset_link = Core\Config()->urls('full') . Core\Router()->toUrl(array(
                    'controller' => 'cms',
                    'action'     => 'renew',
                    'id'         => sha1($user->password . Core\Config()->USER_AUTH['cookie_salt'] . $user->email),
                ));
                
                $mailLabels = Core\Helpers\YAML::get('mails', 'cms');

                $mailForPasswordReset = array(
                    'to_name' => $user->name,
                    'to_mail' => $user->email,
                    'from_name' => Core\Config()->MAILER['identity']['name'],
                    'from_mail' => Core\Config()->MAILER['identity']['email'],
                    'subject' => $mailLabels['reset']['subject'],
                    'content' => $this->getPartialOutput('cms/mails/password_reset'),
                );

                Core\Helpers\Mailer::send($mailForPasswordReset);
                Helpers\FlashMessage::setMessage($labelsReset['success'], 'success');
                Core\Session()->remove('password_reset_error');
            } else {
                $message = isset($this->errors['email']) ? $labelsReset['error'] : $labelsCaptcha['error'];
                Helpers\FlashMessage::setMessage($message, 'danger');

                $captcha = new Captcha\Captcha();
                $captcha->setPublicKey(Core\Config()->CAPTCHA['public_key']);
                $captcha->setPrivateKey(Core\Config()->CAPTCHA['private_key']);

                Core\Session()->set('password_reset_error', true);
                Core\Session()->set('captcha', $captcha);

                $this->captcha = $captcha->html();
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
        $this->renderer->setLayout('public');
        $this->renderer->assets->remove('css/style.css');
        $this->renderer->assets->add('css/login.css');

        $user = Models\CMSUser::find()->where(
            'DATE_ADD(updated_on, INTERVAL 60 MINUTE) > UTC_TIMESTAMP() AND SHA1(CONCAT(password, ?, email)) = ?',
            array(
                Core\Config()->USER_AUTH['cookie_salt'],
                $request->get('id'),
            )
        )->first();

        if ($user) {
            $new_password = Helpers\CMSUsers::generatePassword(10);

            if ($user->save(array('password' => $new_password), true)) {
                $this->new_password = $new_password;
            }
        } else {
            $request->redirectTo(array('controller' => 'cms'));
        }
    }

    /**
     * Login verification gate.
     *
     * @return void
     */
    protected function checkLogged()
    {
        $request = Core\Router()->request;

        if (1 === Core\Session()->get('user_logged')) {
            $this->user = unserialize(rawurldecode(Core\Session()->get('user_info')));

            Core\Registry()->set('current_user', $this->user);
            Core\Helpers\DateTime::setEnvironmentTimezone($this->user->timezone);
        } else {
            $request->redirectTo(array(
                'controller' => 'cms',
                'action'     => 'login',
                'redirect'   => Core\Router()->toUrl(array(
                    'controller' => $this->getControllerName(),
                    'action'     => $this->getActionName(),
                )),
            ));
        }
    }

    /**
     * Skip generation and verification of access scope for the specified controller actions.
     *
     * @param array $actions Array of action names.
     *
     * @return void
     */
    protected function skipAclFor(array $actions)
    {
        $this->skipAclFor = array_merge($this->skipAclFor, $actions);
    }

    /**
     * Permissions verification gate.
     *
     * @see    CMS\Helpers\CMSUsers::userCan()
     *
     * @return boolean
     */
    protected function checkPermissions()
    {
        $controller = $this->getControllerName();
        $action     = $this->getActionName();
        $request    = Core\Router()->request;

        if (!Helpers\CMSUsers::userCan(array('controller' => $controller, 'action' => $action))) {
            $labelsGeneral = Core\Helpers\YAML::get('general');
            Helpers\FlashMessage::setMessage($labelsGeneral['no_access'], 'danger');

            $request->redirectTo(array('controller' => 'users', 'action' => 'account'));
        }

        return true;
    }

    /**
     * Loads CMS accessibility scope.
     *
     * @see    CMS\Helpers\CMSUsers::userCan()
     *
     * @return void
     */
    protected function loadAccessibilityScope()
    {
        $this->modules = array_keys(Core\Helpers\YAML::get('modules'));

        foreach ($this->modules as $key => $module) {
            if (!Helpers\CMSUsers::userCan(array('controller' => $module, 'action' => 'index'))) {
                unset($this->modules[$key]);
            }
        }
    }

    /**
     * Load vendor assets across the CMS.
     *
     * @return void
     */
    protected function loadVendorAssets()
    {
        $this->renderer->assets->add(array(
            'vendor/jquery/dist/jquery.min.js',
            'vendor/bootstrap/dist/js/bootstrap.min.js',
            'vendor/bootstrap/dist/css/bootstrap.css',
            'vendor/bootstrap-chosen/js/chosen.jquery.min.js',
            'vendor/bootstrap-chosen/css/chosen.min.css',
            'vendor/ekko-lightbox/dist/ekko-lightbox.min.js',
            'vendor/ekko-lightbox/dist/ekko-lightbox.min.css',
            'vendor/bootbox/bootbox.js',
            'vendor/spin.js/spin.js',
            'css/bootstrap-theme.athlon.css',
        ));
    }

    /**
     * Load CMS mode assets.
     *
     * @return void
     */
    protected function loadCmsAssets()
    {
        $this->renderer->assets->add(array(
            'js/cms.js',
            'js/init.js',
            'css/style.css',
        ));
    }

    /**
     * Loads form builder generator assets.
     *
     * @access protected
     *
     * @return void
     */
    protected function loadFormAssets()
    {
        $this->renderer->assets->add(array(
            'vendor/moment/min/moment.min.js',
            'vendor/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
            'vendor/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
            'vendor/bootstrap-maxlength/bootstrap-maxlength.min.js',
            'vendor/pnikolov-bootstrap-daterangepicker/daterangepicker.js',
            'vendor/pnikolov-bootstrap-daterangepicker/daterangepicker-bs3.css',
            'vendor/pnikolov-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js',
            'vendor/pnikolov-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css',
        ));
    }

    /**
     * Loads data table listing assets.
     *
     * @access protected
     *
     * @return void
     */
    protected function loadListingAssets()
    {
        $this->renderer->assets->add(array(
            'vendor/moment/min/moment.min.js',
            'vendor/pnikolov-bootstrap-daterangepicker/daterangepicker.js',
            'vendor/pnikolov-bootstrap-daterangepicker/daterangepicker-bs3.css',
            'vendor/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
            'vendor/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
            'vendor/jquery-serialize-object/dist/jquery.serialize-object.min.js',
            'js/libs/obj.js',
            'js/libs/datatables.js',
        ));
    }
}
