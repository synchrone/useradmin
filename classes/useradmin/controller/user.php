<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * User controller: user administration, also user accounts/profiles.
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */
class Useradmin_Controller_User extends Controller_App {

	/**
	 * @var string Filename of the template file.
	 */
	public $template = 'template/useradmin';

	/**
	 * Controls access for the whole controller, if not set to FALSE we will only allow user roles specified.
	 *
	 * See Controller_App for how this implemented.
	 *
	 * Can be set to a string or an array, for example array('login', 'admin') or 'login'
	 */
	public $auth_required = FALSE;

	/** Controls access for separate actions
	 *
	 * See Controller_App for how this implemented.
	 *
	 * Examples:
	 * 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
	 * 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
	 */
	public $secure_actions = array(
		// user actions
		'index' => 'login', 
		'profile' => 'login', 
		'profile_edit' => 'login', 
		'unregister' => 'login',
		'change_password' => 'login'
	); // the others are public (forgot, login, register, reset, noaccess)
	// logout is also public to avoid confusion (e.g. easier to specify and test post-logout page)
	
	/** User Model Fields
	 * Override in your app to add fields
	 */
	public $user_model_fields = array(
		'username', 
		'password', 
		'email'
	);

    public function before() 
	{
		$fullBaseUrl = Url::base(true);

		//was user on our site?
        if( strpos($this->request->referrer(), $fullBaseUrl) === 0 )
		{
			//now check that a controller set, it wasn't the user controller, and that the session var "noReturn" is not false
			
            $uri = parse_url($this->request->referrer(), PHP_URL_PATH);
			
			// correct the path for url_base and index_file, in part taken from Kohana_Request::detect_uri()
			// Get the path from the base URL, including the index file
			$base_url = parse_url(Kohana::$base_url, PHP_URL_PATH);
			
			if (strpos($uri, $base_url) === 0)
			{
				// Remove the base URL from the URI
				$uri = (string) substr($uri, strlen($base_url));
			}

			if (Kohana::$index_file AND strpos($uri, Kohana::$index_file) === 0)
			{
				// Remove the index file from the URI
				$uri = (string) substr($uri, strlen(Kohana::$index_file));
			}

            $processedRef = Request::process_uri($uri);
			
            $referrerController = Arr::path(
                $processedRef,
                'params.controller',
                false
            );

            if($referrerController && $referrerController != 'user' && !Session::instance()->get('noReturn',false)){
                Session::instance()->set('returnUrl',$this->request->referrer());
            }

        }

        parent::before();
        
    }

	// USER SELF-MANAGEMENT
	/**
	 * View: Redirect admins to admin index, users to user profile.
	 */
	public function action_index()
	{
		// if the user has the admin role, redirect to admin_user controller
		if (Auth::instance()->logged_in('admin'))
		{
			$this->request->redirect('admin_user/index');
		}
		else
		{
			$this->request->redirect('user/profile');
		}
	}

	/**
	 * View: Access not allowed.
	 */
	public function action_noaccess()
	{
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('access.denied');
		$view = $this->template->content = View::factory('user/noaccess');
	}

	/**
	 * View: User account information
	 */
	public function action_profile()
	{
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('user.profile');
		if (Auth::instance()->logged_in() == false)
		{
			// No user is currently logged in
			$this->request->redirect('user/login');
		}
		$view = $this->template->content = View::factory('user/profile');
		// retrieve the current user and set the view variable accordingly
		$view->set('user', Auth::instance()->get_user());
	}

	/**
	 * View: Profile editor
	 */
	public function action_profile_edit($redirect_url = 'user/profile')
	{
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('edit.profile');
        /** @var Model_User $user */
		$user = Auth::instance()->get_user();
		$id = $user->id;
		// load the content from view
		$view = View::factory('user/profile_edit');
		// save the data
		if (! empty($_POST) && is_numeric($id))
		{
			if (empty($_POST['password']) || empty($_POST['password_confirm']))
			{
				// force unsetting the password! Otherwise Kohana3 will automatically hash the empty string - preventing logins
				unset($_POST['password'], $_POST['password_confirm']);
			}
			try
			{
				$user->update_user($_POST, $this->user_model_fields);
				// message: save success
				Message::add('success', __('values.saved').'.');
				// redirect and exit
				$this->request->redirect($redirect_url);
				return;
			}
			catch (ORM_Validation_Exception $e)
			{
				// Get errors for display in view
				// Note how the first param is the path to the message file (e.g. /messages/register.php)
				Message::add('error', __('error.values.could.not.be.saved'));
				$errors = $e->errors('register');
				$errors = array_merge($errors, ( isset($errors['_external']) ? $errors['_external'] : array() ));
				$view->set('errors', $errors);
				// Pass on the old form values
				$user->password = '';
				$view->set('data', $user);
			}
		}
		else
		{
			// load the information for viewing
			$view->set('data', $user);
		}
		// retrieve roles into array
		$roles = array();
		foreach ($user->roles->find_all() as $role)
		{
			$roles[$role->name] = $role->description;
		}
		$view->set('user_roles', $roles);
		$view->set('id', $id);
		$this->template->content = $view;
	}

    protected function registration_optional_checks(View $view)
    {
        // if configured to use captcha, check the reCaptcha result
        if (Kohana::$config->load('useradmin')->captcha)
        {
            $recaptcha_config = Kohana::$config->load('recaptcha');
            $view->set('captcha_enabled', true);
            $recaptcha_resp = recaptcha_check_answer(
                $recaptcha_config['privatekey'],
                $_SERVER['REMOTE_ADDR'],
                $_POST['recaptcha_challenge_field'],
                $_POST['recaptcha_response_field']
            );

            /** @var $captcha_validation Validation */
            $captcha_validation = Validation::factory((array) $recaptcha_resp)
                ->rule('is_valid','not_empty')
                ->rule('is_valid','equals',array(':value',true));
            if (! $captcha_validation->check())
            {
                $view->set('recaptcha_html',
                    recaptcha_get_html(
                        $recaptcha_config['publickey'],
                        $recaptcha_resp->error
                    )
                );

                throw new ORM_Validation_Exception('register', $captcha_validation, __('captcha.incorrect').'. '.__('please.try.again').'.');
            }
        }
    }

	/**
	 * Register a new user.
	 */
	public function action_register()
	{
		if(!Kohana::$config->load('useradmin.register_enabled'))
			$this->request->redirect('user/login');
		// Load reCaptcha if needed
		if (Kohana::$config->load('useradmin')->captcha)
		{
			include Kohana::find_file('vendor', 'recaptcha/recaptchalib');
			$recaptcha_config = Kohana::$config->load('recaptcha');
			$recaptcha_error = null;
//            get recaptcha html code as follows:
//            $recaptcha = recaptcha_get_html($recaptcha_config->publickey, $recaptcha_error);
		}
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('user.registration');
		// If user already signed-in
		if (Auth::instance()->logged_in() != false)
		{
			// redirect to the user account
			$this->request->redirect('user/profile');
		}
		// Load the view
		$view = View::factory('user/register');
		// If there is a post and $_POST is not empty
		if ($_POST)
		{
			try
			{
                /** @var $auth Useradmin_Auth_ORM */
                $auth = Auth::instance();
                $this->registration_optional_checks($view);
                $new_user = $auth->register($_POST, TRUE);

                if($provider_info = Session::instance()->get_once('registration_provider_data')){
                    ORM::factory('User_Identity')
                        ->values(array('user_id' => $new_user->id)+ $provider_info)
                        ->save();
                }

				// sign the user in
                $auth->force_login($new_user);

				// redirect to the user account
				$this->request->redirect(Session::instance()->get_once('returnUrl','user/profile'));
			}
			catch (ORM_Validation_Exception $e)
			{
				// Get errors for display in view
				// Note how the first param is the path to the message file (e.g. /messages/register.php)
				$errors = $e->errors('register');
				// Move external errors to main array, for post helper compatibility
				$errors = array_merge($errors, ( isset($errors['_external']) ? $errors['_external'] : array() ));
				$view->set('errors', $errors);
				// Pass on the old form values
				$_POST['password'] = $_POST['password_confirm'] = '';
				$view->set('defaults', $_POST);
			}
		}

		$this->template->content = $view;
	}

	/**
	 * Close the current user's account.
	 */
	public function action_unregister()
	{
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('close.user.account');
		if (Auth::instance()->logged_in() == false)
		{
			// No user is currently logged in
			$this->request->redirect('user/login');
		}
		// get the user id
		$id = Auth::instance()->get_user()->id;
		$user = ORM::factory('user', $id);

		// KO3 ORM is lazy loading, which means we have to access a single field to actually have something happen.
		if ($user->id != $id)
		{
			// If the user is not the current user, redirect
			$this->request->redirect('user/profile');
		}

		// check for confirmation
		if (is_numeric($id) && isset($_POST['confirmation']) && $_POST['confirmation'] == 'Y')
		{
			if (Auth::instance()->logged_in())
			{
				// Log the user out, their account will no longer exist
				Auth::instance()->logout();
			}
			// Delete the user
			$user->delete($id);
			// Delete any associated identities
			DB::delete('user_identities')->where('user_id', '=', $id)
			                           ->execute();
			// message: save success
			Message::add('success', __('user.deleted').'.');
			$this->request->redirect(Session::instance()->get_once('returnUrl','user/profile'));
		}
		// display confirmation
		$this->template->content = View::factory('user/unregister')
			->set('id', $id)
			->set('data', array('username' => Auth::instance()->get_user()->username));
	}

	/**
	 * View: Login form.
	 */
	public function action_login()
	{
		// ajax login
		if ($this->request->is_ajax() && isset($_REQUEST['username'], $_REQUEST['password']))
		{
			$this->auto_render = false;
			$this->request->headers('Content-Type', 'application/json');

			if ( Auth::instance()->logged_in() != 0 )
			{
				$this->response->status(200);
				$this->template->content = $this->request->body('{ "success": "true" }');
				return;
			}
			else {
				if (Auth::instance()->login($_REQUEST['username'],$_REQUEST['password'],
                                            Arr::get($_REQUEST,'remember',false)!=false)
                ){
					$this->response->status(200);
					$this->template->content = $this->request->body('{ "success": "true" }');
					return;
				}
            }

			$this->response->status(500);
			$this->template->content = $this->request->body('{ "success": "false" }');
			return;
		}
		else
		{
			// set the template title (see Controller_App for implementation)
			$this->template->title = __('login');
			
			// If user already signed-in
			if (Auth::instance()->logged_in() != 0)
			{
				// redirect to the user account
				$this->request->redirect(Session::instance()->get_once('returnUrl','user/profile'));
			}
			
			$view = View::factory('user/login');
			
			// If there is a post and $_POST is not empty
			if ($_REQUEST && isset($_REQUEST['username'], $_REQUEST['password']))
			{
				// Check Auth if the post data validates using the rules setup in the user model
				if (Auth::instance()->login($_REQUEST['username'], $_REQUEST['password'],
                                            Arr::get($_REQUEST,'remember',false)!=false)
                ){
					// redirect to the user account
					$this->request->redirect(Session::instance()->get_once('returnUrl','user/profile'));
					return;
				}
				else
				{
					$view->set('username', $_REQUEST['username']);
					// Get errors for display in view
					$validation = Validation::factory($_REQUEST)
						->rule('username', 'not_empty')
						->rule('password', 'not_empty');
					if ($validation->check())
					{
						$validation->error('password', 'invalid');
					}
					$view->set('errors', $validation->errors('login'));
				}
			}
			
			// allow setting the username as a get param
			if (isset($_GET['username']))
			{
				$view->set('username', htmlspecialchars($_GET['username']));
			}
            $view->set('providers',Kohana::$config->load('useradmin.providers'));
			$this->template->content = $view;
		}
	}

	/**
	 * Log the user out.
	 */
	public function action_logout()
	{
		// Sign out the user
		Auth::instance()->logout();
		// redirect to the user account and then the signin page if logout worked as expected
		$this->request->redirect(Session::instance()->get_once('returnUrl','user/profile'));
	}

	/**
	 * A basic implementation of the "Forgot password" functionality
	 */
	public function action_forgot()
	{
		// Password reset must be enabled in config/useradmin.php
		if (! Kohana::$config->load('useradmin')->email)
		{
			Message::add('error', 'email.password.reset.not.enabled');
			$this->request->redirect('user/register');
		}
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('forgot.password');
		if (isset($_POST['reset_email']))
		{
            /**
             * @var $user Model_User
             */
            $user = ORM::factory('user')->where('email', '=', $_POST['reset_email'])->find();
			// admin passwords cannot be reset by email
			if (is_numeric($user->id) && !$user->has('roles', ORM::factory('role', array('name' => 'admin'))))
			{
				// send an email with the account reset token
				$user->reset_token = $user->generate_password(32);
				$user->save();

                //create all email fields we need
				$subject = __('account.password.reset');
				$to = $_POST['reset_email'];
				$from = Kohana::$config->load('useradmin')->email_address;
				$from_name = Kohana::$config->load('useradmin')->email_address_name;

				$body = __("email.password.reset.message.body", array(
					':reset_token_link' => URL::site('user/reset?reset_token='.$user->reset_token, TRUE),
					':reset_link' => URL::site('user/reset', TRUE), 
					':reset_token' => $user->reset_token, 
					':username' => $user->username
				));

                $mail = Email::factory($subject, $body)
                    ->to($to)
                    ->from($from, $from_name);

                $failed = array();
                $mail->send($failed);

				if ( !count($failed) )
				{
					Message::add('success', __('password.reset.email.sent').'.');
					$this->request->redirect('user/login');
				}
				else
				{
					Message::add('error', __('could.not.send.email').'.');
				}
			}
			else
            {
				if ($user->username == 'admin')
				{
					Message::add('error', __('no.admin.account.email.password.reset'));
				}
				else
				{
					Message::add('error', __('user.account.not.found'));
				}
            }
		}
		$this->template->content = View::factory('user/reset/forgot');
	}

	/**
	 * A basic version of "reset password" functionality.
	 */
	function action_reset()
	{
		// Password reset must be enabled in config/useradmin.php
		if (! Kohana::$config->load('useradmin')->email)
		{
			Message::add('error', __('email.password.reset.not.enabled'));
			$this->request->redirect('user/register');
		}
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('reset.password');
		if (isset($_REQUEST['reset_token']))
		{
			// make sure that the reset_token has exactly 32 characters (not doing that would allow resets with token length 0)
			if (( strlen($_REQUEST['reset_token']) == 32 ))
			{
				$user = ORM::factory('user')
					->where('reset_token', '=', $_REQUEST['reset_token'])
					->find();

				// The admin password cannot be reset by email
				if ($user->has('roles',ORM::factory('role',array('name'=>'admin'))))
				{
					Message::add('error', __('no.admin.account.email.password.reset'));
				}
				else
                {
					if (is_numeric($user->id) && ( $user->reset_token == $_REQUEST['reset_token'] ))
					{
						$password = $user->generate_password();
						$user->password = $password;
						// This field does not exist in the default config:
						//               $user->failed_login_count = 0;
						$user->save();
						Message::add('success', '<p>' 
						                      . __('your.new.password.is :password', array(':password' => $password))
						                      . '</p><p>' 
						                      . __('please.log.in.below') 
						                      . '</p>'
						);
						$this->request->redirect('user/login?username=' . urlencode($user->username));
					}
                }
			}
		}
		$this->template->content = View::factory('user/reset/reset');
	}

	/**
	 * Allow the user to change their password.
	 */
	function action_change_password()
	{
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('change.password');
        /** @var Model_User $user */
		$user = Auth::instance()->get_user();
		$id = $user->id;
		// load the content from view
		$view = View::factory('user/change_password');
		// save the data
		if (count($this->request->post()) != 0 && is_numeric($id))
		{
			// editing requires that the username and email do not exist (EXCEPT for this ID)
			// If the post data validates using the rules setup in the user model
			try{
                $user->update_user($this->request->post(),array('password','password_confirm'));
				// message: save success
				Message::add('success', __('values.saved'));
				// redirect and exit
				$this->request->redirect('user/index'); //index will redir ya whereever you need
			}
            catch(Exception $e)
            {
				// UNFORTUNATELY, it is NOT possible to get errors for display in view
				// since they will never be returned by change_password()
				Message::add('error', __('unable.to.change.password.?passwords.match'));
			}
		}
		else
		{
			// load the information for viewing
			$view->set('data', $user->as_array());
		}
		$this->template->content = $view;
	}

	/**
	 * Redirect to the provider's auth URL
	 * @param string $provider
	 */
	function action_provider()
	{
		if (Auth::instance()->logged_in())
		{
			Message::add('success', __('already.logged.in'));
			// redirect to the user account
			$this->request->redirect('user/profile');
		}
        $provider_name = $this->request->param('provider');
		$provider = Provider::factory($provider_name);
		if ($this->request->query('code') && $this->request->query('state'))
		{
			$this->action_provider_return($provider_name);
			return;
		}
		if (is_object($provider))
		{
			$this->request->redirect(
			    $provider->redirect_url(Route::url('user/default',array('action'=>'provider_return', 'provider' => $provider_name),true))
            );
			return;
		}
		Message::add('error', __('provider.not.enabled.select.different.or.login'));
		$this->request->redirect('user/login');
		return;
	}

	function action_associate()
	{
		$provider_name = $this->request->param('provider');
		if ($this->request->query('code') && $this->request->query('state'))
		{
			$this->action_associate_return($provider_name);
			return;
		}
		if (Auth::instance()->logged_in())
		{
			if ($this->request->post('confirmation') == 'Y')
			{
                /** @var Provider $provider */
				$provider = Provider::factory($provider_name);
				if (is_object($provider))
				{
					$this->request->redirect(
                        $provider->redirect_url(Route::url('user/default',array('action'=>'associate_return', 'provider' => $provider_name), true))
                    );
					return;
				}
				else
				{
					Message::add('error', __('provider.not.enabled.select.different.or.login'));
					$this->request->redirect('user/login');
					return;
				}
			}
			else 
				if (isset($_POST['confirmation']))
				{
					Message::add('error', __('click.yes.to.associate.account'));
					$this->request->redirect('user/profile');
					return;
				}
		}
		else
		{
			Message::add('error', 'not.logged.in');
			$this->request->redirect('user/login');
			return;
		}
		$this->template->content = View::factory('user/associate')->set('provider_name', $provider_name);
	}

	/**
	 * Associate a logged in user with an account.
	 *
	 * Note that you should not trust the OAuth/OpenID provider-supplied email
	 * addresses. Yes, for Facebook, Twitter, Google and Yahoo the user is actually
	 * required to ensure that the email is in fact one that they control.
	 *
	 * However, with generic OpenID (and non-trusted OAuth providers) one can setup a
	 * rogue provider that claims the user owns a particular email address without
	 * actually owning it. So if you trust the email information, then you open yourself to
	 * a vulnerability since someone might setup a provider that claims to own your
	 * admin account email address and if you don't require the user to log in to
	 * associate their account they gain access to any account.
	 *
	 * TL;DR - the only information you can trust is that the identity string is
	 * associated with that user on that openID provider, you need the user to also
	 * prove that they want to trust that identity provider on your application.
	 *
	 */
	function action_associate_return($redirect_url = '/user/profile')
	{
		$provider_name = $this->request->param('provider');
		if (Auth::instance()->logged_in())
		{
			$provider = Provider::factory($provider_name);
			// verify the request
			if (is_object($provider) && $provider->verify())
			{
				$user = Auth::instance()->get_user();
				if ($user->loaded() && is_numeric($user->id))
				{
					if (Auth::instance()->logged_in() && Auth::instance()->get_user()->id == $user->id)
					{
						// found: "merge" with the existing user
						$user_identity = ORM::factory('user_identity');
						$user_identity->user_id = $user->id;
						$user_identity->provider = $provider_name;
						$user_identity->identity = $provider->user_id();
						if ($user_identity->validation()->check())
						{
							Message::add('success', __('user.account.associated.with.provider'));
							$user_identity->save();
						}
						else
						{
							Message::add('error', __('unable.to.associate.account.with.provider'));
						}
                        $this->request->redirect($redirect_url);
                        return;
					}
				}
			}
		}
		Message::add('error', __('error.associating.account.with.provider'));
		$this->request->redirect($redirect_url);
		return;
	}

	/**
	 * Allow the user to login and register using a 3rd party provider.
	 */
	function action_provider_return()
	{
		$provider_type = $this->request->param('provider');
        /** @var Provider $provider */
		$provider = Provider::factory($provider_type);
		if (! is_object($provider))
		{
			Message::add('error', __('provider.not.enabled.select.different.or.login'));
			$this->request->redirect('user/login');
			return;
		}
		// verify the request
		if ($provider->verify())
		{
            $provider_info = array(
                'provider' => $provider_type,
                'identity' => $provider->user_id(),
                'email' => $provider->email(),
                'name' => $provider->name()
            );

			// check for previously connected user
			$user_identity = ORM::factory('user_identity')
				->where('provider', '=', $provider_info['provider'])
				->and_where('identity', '=', $provider_info['identity'])
				->find();
			if ($user_identity->loaded())
			{
				$user = $user_identity->user;
				if ($user->loaded())
				{
					// found, log user in
                    /** @var Auth_ORM $auth */
                    $auth = Auth::instance();
					$auth->force_login($user);
					// redirect to the user account
					$this->request->redirect(Session::instance()->get_once('returnUrl','user/profile'));
					return;
				}
			}

			// If register is disabled, don't create new account
			if(!Kohana::$config->load('useradmin.register_enabled')){
				$this->request->redirect('user/login');
            }

            $this->create_user_from_provider($provider_info);
        }
		else
		{
			Message::add('error', __('retrieving.info.from.provider.failed.register.below'));
			$this->request->redirect('user/login');
		}
	}

    function action_provider_disconnect($redirect_url = 'user/profile') //Note: this is for child override
    {
        /**
         * @var Model_User $user
         */
        if(!($user = Auth::instance()->get_user()))
        {
            $this->request->redirect('user/login');
        }
        if($provider_name = $this->request->param('provider'))
        {
            $identity = ORM::factory('User_Identity', array('provider'=>$provider_name,'user_id'=>$user->id));
            if($identity->loaded()){
                $identity->delete();
            }
        }
        $this->request->redirect($redirect_url);
    }

	/**
	 * Media routing code. Allows lazy users to load images via Kohana. See also: init.php.
	 * I recommend just serving the files via apache, e.g. copy the public directory to your webroot.
	 */
	public function action_media()
	{
		// prevent auto render
		$this->auto_render = FALSE;
		// Generate and check the ETag for this file
		//		$this->request->check_cache(sha1($this->request->uri));
		// Get the file path from the request
		$file = Request::current()->param('file');
		$dir = Request::current()->param('dir');
		// Find the file extension
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		// Remove the extension from the filename
		$file = substr($file, 0, - ( strlen($ext) + 1 ));
		$file = Kohana::find_file('public', $dir . '/' . $file, $ext);
		if ($file)
		{
			// Send the file content as the response
			$this->response->body(file_get_contents($file));
			
			// Set the proper headers to allow caching
    		$this->response->headers('Content-Type', File::mime_by_ext($ext));
    		$this->response->headers('Content-Length', (string) filesize($file));
    		$this->response->headers('Last-Modified', date('r', filemtime($file)));
		}
		else
		{
			// Return a 404 status
			$this->response->status(404);
		}

	}

    protected function create_user_from_provider($provider_info)
    {
        // create new account
        if (Auth::instance()->logged_in()) {
            //user came back from a login-via-provider, but was already auth'd locally
            Message::add('error', __('logged.in.but.account.emails.do.not.match'));
            $this->request->redirect('user/profile');
        }

        /** @var $user Useradmin_Model_User */
        $user = ORM::factory('user');

        // fill in values
        // generate long random password (maximum that passes validation is 42 characters)
        $password = $user->generate_password(42);
        $user_info = array(
            // get a unused username like firstname.surname or firstname.surname2 ...
            'username' => $user->generate_username($provider_info['name']),
            'password' => $password,
            'password_confirm' => $password
        );

        if (Valid::email($provider_info['email'], TRUE)) {
            $user_info['email'] = $provider_info['email'];
        }
        try {
            // If the post data validates using the rules setup in the user model
            $user->create_user($user_info, $this->user_model_fields);
            // Add the login role to the user (add a row to the db)
            $login_role = new Model_Role(array(
                'name' => 'login'
            ));
            $user->add('roles', $login_role);

            // create user identity after we have the user id
            ORM::factory('user_identity')
                ->values(array('user_id' => $user->id) + $provider_info)
                ->save();

            // sign the user in
            Auth::instance()->login($user_info['username'], $password);
            // redirect to the user account
            $this->request->redirect(Session::instance()->get_once('returnUrl', 'user/profile'));

        } catch (ORM_Validation_Exception $e) { //since we checked on username and password that only leaves us with duplicate or empty email

            if (key($e->errors()) == 'email' && Arr::path($e->errors(), 'email.0') == 'unique') {
                //getting the user this social-auth request claims to drive
                $user = ORM::factory('User')
                    ->where('email', '=', $user_info['email'])
                    ->find();

                if ($user->has('roles', ORM::factory('role', array('name' => 'admin'))) ||
                    !Kohana::$config->load('useradmin')->believe_remote_email
                ) {
                    /**
                     * Redirect back to the front page in case they
                     * try to create another account with a separate provider
                     */
                    Message::add('error', __('matching.account.exists.for.provider'));
                    $this->request->redirect('user/login');
                }

                //so we believe the remote auth method email and force login users even if they didn't associate with it
                ORM::factory('user_identity')
                    ->values(array('user_id' => $user->id) + $provider_info)
                    ->save();

                /**
                 * @var $auth Auth_ORM
                 */
                $auth = Auth::instance();
                $auth->force_login($user, true);
                $this->request->redirect(Session::instance()->get_once('returnUrl', 'user/profile'));
            }

            if ($provider_info['email'] === null) {
                Message::add('error', __('no.email.retrive.support'));
            } else {
                Message::add('error', __('please.complete.data.from.other.account'));
            }


            // in case the data for some reason fails, the user will still see something sensible:
            // the normal registration form.
            $view = View::factory('user/register');
            Session::instance()->set('registration_provider_data', $provider_info);

            $errors = $e->errors('register');
            // Move external errors to main array, for post helper compatibility
            $errors = array_merge($errors, (isset($errors['_external']) ? $errors['_external'] : array()));
            $view->set('errors', $errors);

            // Pass on the old form values
            $user_info['password'] = $user_info['password_confirm'] = '';
            $view->set('defaults', $user_info);

            if (Kohana::$config->load('useradmin')->captcha) {
                // FIXME: Is this the best place to include and use recaptcha?
                include Kohana::find_file('vendor', 'recaptcha/recaptchalib');
                $recaptcha_config = Kohana::$config->load('recaptcha');
                $recaptcha_error = null;
                $view->set('captcha_enabled', true);
                $view->set('recaptcha_html', recaptcha_get_html($recaptcha_config['publickey'], $recaptcha_error));
            }
            $this->template->content = $view;
        }

    }
}
