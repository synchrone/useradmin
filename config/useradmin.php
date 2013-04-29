<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'register_enabled' => true,
    /**
     * The number of failed logins allowed can be specified here:
     * If the user mistypes their password X times, then they will not be permitted to log in during the jail time.
     * This helps prevent brute-force attacks.
     */
    'auth' => array(

      /**
       * Define the maximum failed attempts to login
       * set 0 to disable the login jail
       */
      'max_failed_logins' => 5,

      /**
       * Define the time that user who archive the max_failed_logins will need to 
       * wait before his next attempt
       */
      'login_jail_time' => "5 minutes",

      /**
       * You need to set a non empty cookie salt string in order to enable the "remember.me" option for login
       *
       * You can do this here or you set it in the bootstrap.php file like so:
       * Cookie::$salt = 'your salt ..';
       *
       * A salt set in bootstrap.php takes precedence over the one set in the useradmin config
       */
      'cookie_salt' => '',

    ),

    /**
     * 3rd party providers supported/allowed.
     */
    'providers' => array(

       /**
        * Toggle Facebook support: if set, then users can log in using Facebook.
        *
        * Setup:
        * - You need the extra table from schema.sql for storing 3rd party identifiers
        * - You must register your app with FB and add the information in /config/facebook.php
        * - You must have the Facebook SDK at /vendors/facebook/src/facebook.php (bundled in the default repo)
        *
        */
       'facebook' => true,

       /**
        * Toggle Twitter support: if set, users can log in using Twitter
        *
        * Setup:
        * - You need the extra table from schema.sql for storing 3rd party identifiers
        * - You must register your app with Twitter and add the information in /config/oauth.php (Kohana-Oauth's config)
        * - You must enable the Kohana Core oauth module
        */
       'twitter' => true,

       /**
        * Toggle Google support: if set, users can log in using their Google account.
        *
        * Setup:
        * - You need the extra table from schema.sql for storing 3rd party identifiers
        * - You must have LightOpenID in /vendors/lightopenid/openid.php (bundled in the repo)
        */
       'google' => true,

       /**
        * Toggle Yahoo support: if set, users can log in using their Yahoo account.
        *
        * Setup:
        * - You need the extra table from schema.sql for storing 3rd party identifiers
        * - You must have LightOpenID in /vendors/lightopenid/openid.php (bundled in the repo)
        */
       'yahoo' => true,

       /**
        * Toggle Linkedin support: if set, users can log in using their Linkedin account.
        *
        * Setup:
        * - You need the extra table from schema.sql for storing 3rd party identifiers
        * - You must enable the Kohana Core oauth module
        * - You must register your app with Linkedin and add the information in /config/oauth.php (Kohana-Oauth's config)
        * - You may want to specify scope in oauth config, available by key 'linkedin.scope'
        */
       'linkedin' => true,
    ),

    /**
     * Toggle email support: if set, then users (except admins) can reset user accounts via email.
     * They will be sent an email with a reset token, which they enter, then their password will be reset to a new random password.
     *
     * Setup:
     * - You must have the Kohana-email module enabled (bundled in default repo)
     */
    'email' => true,

    /* change this to the email address and name you want the password reset emails to come from. */
    'email_address' => 'no-response@example.com',
    'email_address_name' => 'Your Sender Name',

    /**
     * Require email for users ( turn off to enable instant login for services, which provide no email (twitter, vk.com, etc)
     * Notice: for this to work, you should change database field to allow null
     */
    'remote_auth_require_email' => true,
    /**
     * Toggle reCaptcha support: if set, then during registration the user is shown
     * a reCaptcha which they must answer correctly (unless they are using one of the 3rd party accounts).
     *
     * Setup
     * - You must have the reCaptcha library (e.g. http://recaptcha.net) in your vendors directory. (bundled in the default repo)
     * - You must set the private and public key in /config/recaptcha.php from https://www.google.com/recaptcha/admin/create
     */
    'captcha' => false,
);
