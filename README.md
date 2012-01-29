Kohana-Useradmin Module
=======================

This module gives a simple but complete user authentication solution. Your users can register, login/logout,
view their profile details and change them, as well as request password-reset links via email.
A user with admin role can administer users, create, edit and delete them on the admin page.

**NEW: Upgraded to support Kohana 3.2.x!**

### INSTALLATION NOTES ###
**Database:** Use the schema.sql to create all needed tables in your database,
be sure to backup all your tables that have the same names as those created by the useradmin module.
Although the schema is based on the schema of the official Kohana auth/orm modules it slightly differs and has additional tables!

In case you already used the Kohana auth schema you may want to check if the password hashes used are compatible with the useradmin ones.

**Enable this module before the Kohana Auth and ORM modules or it will not work.**
The useradmin module must shadow the classes Auth_ORM and Model_User as well as
make the useradmin config available to the Auth class.

	Kohana::modules(array(
		'useradmin'  => MODPATH.'useradmin',  // Mixu's useradmin module
	 	'auth'       => MODPATH.'auth',       // Basic authentication
	 	'orm'        => MODPATH.'orm',       // Basic authentication
		...
	));

For the pagination module I had success with the 3.2/devel branch of Shadowhands github repo here: <https://github.com/shadowhand/pagination/tree/3.2/develop>, the default 3.1/master branch didn't work.
If you are using submodules the following can be used (and don't forget adding it to Kohana modules afterwards) to checkout the needed branch:

	git submodule add git://github.com/shadowhand/pagination.git modules/pagination
	cd modules/pagination
	git checkout -b 3.2/develop origin/3.2/develop
	cd ../..
	git submodule init && git submodule update

**Language translations**

You should set your default target language to something other than "en-us" (eg. "en" will work fine), or the translation files will not work.
That is because when using the __() translation function for output, Kohana just returns the unaltered message keys because it thinks these are already "en-us".
Translations have been added using the kohana-i18nget module that easily extracts all translation strings and creates a language file for you:
<https://github.com/rafsoaken/kohana-i18nget>

If a language you fancy is not yet included in the useradmin module, we recommend adding the kohana-i18nget module (don't forget to enable it),
then adding the new language to the kohana-i18nget config file and then calling from your project root(from commandline):

    php index.php --uri=i18nget/generate/useradmin/useradmin
    # the first useradmin is where translation strings are searched for, the second is the target folder for the i18n files

This generates the translation file for the new language in MODPATH/useradmin/i18n/lang.php with all the missing translation keys that are used in the useradmin module.
Translations for a new language should then not take more than half an hour, and you are ready to go. Please consider making your translation available to the project.


### TUTORIALS ###

NEW: Upgraded to support Kohana 3.2.x!

NEW: Upgraded to support Kohana 3.1.x! Thank you gartz for this! If you need KO 3.0.x support, use the Feb 27th version.

Most recent, detailed tutorial - note, documentation still needs an update:

* [Getting started with Useradmin, my Kohana 3 auth admin module](http://blog.mixu.net/2011/01/13/getting-started-with-useradmin-my-kohana-3-auth-admin-module/)

Earlier writing about Kohana auth:

* [Kohana 3 auth sample implementation and documentation](http://blog.mixu.net/2010/09/14/kohana-3-auth-sample-implementation-and-documentation/)
* [Kohana 3 auth: the auth module functionality](http://blog.mixu.net/2010/09/07/kohana-3-auth-the-auth-module-functionality/)
* [Step-by-step guide to getting started with Kohana 3 auth](http://blog.mixu.net/2010/09/06/step-by-step-guide-to-kohana-3-auth/)


### SUPPORTED PROVIDERS ###

+ Regular Kohana user account registration
+ Facebook (OAuth v2.0)
+ Twitter (OAuth v1.0)
+ Google (OpenID)
+ Yahoo (OpenID)

### MODULES USED ###

* Core: Auth, ORM, Database
* Core (but not included by default in KO 3.1): pagination
* Other: kohana-email, kohana-oauth, fleepme-unittest.

### CONTRIBUTORS ###

I would like to thank:

* [jnbdz](https://bitbucket.org/jnbdz/useradmin/) for adding the reCaptcha support.
* [gartz](https://github.com/gartz/useradmin/) for doing upgrading the module to support Kohana 3.1.x.

### CHANGELOG ###

Jan 29th 2011:

* Upgraded for Kohana 3.2
* Better Installation instructions for less frustration
* Added english and german translation files

March 19th 2011:

* Upgraded to support Kohana 3.1.x! Thanks gartz!
* Testing various actions to ensure KO 3.1.2 compatibility (admin in particular), improved validation message handling.
* Added support for media file routing to make it easier to get started with the module.

Feb 27th 2011:

* New UI! The UI is loosely based on https://github.com/pilu/web-app-theme (a theme generator for Ruby) and is a lot better looking than the old one. See screenshots below.
* Twitter login! Requires that you enable the kohana-oauth module from the core. 
* Google and Yahoo login! Uses LightOpenID which is bundled in the repo. I had a look at Janrain's popular library, but to me if you let your library generate errors on PHP 5.0.0 - 5.3.0 and have dozens of pending issues and pull requests - then you just don't care about supporting your library. So not using Janrain's lib.
* Refactored 3rd party account provider code for better extensibility.
* Changed from Facebook Javascript API (which had some reports of problems) to PHP/redirect based API. No popups during login, just a redirect!
* Database changes to allow user to be associated with more service providers. See MIGRATION below if you used had the Facebook login enabled previously and want to migrate the accounts.
* Included Datatable helper which helps when working with pagination links in tables.

Feb 21st 2011:

* CRITICAL SECURITY BUG in Facebook login; it is strongly recommended that all users of Facebook login functionality upgrade (does not affect sites with Facebook login disabled). Thank you Euan McKay!
* To support login via AJAX requests moved the processing of results from the Auth check to Controller_App->access_required and Controller_App->login_required. Override those in your app, using Request::$is_ajax to detect AJAX requests.
* Workaround for email field for http://dev.kohanaframework.org/issues/3750 in messages/register.php

Jan 28th 2011:

* Added reCaptcha support (captcha on registration)
* Upgraded to Kohana v3.0.9

Jan 12th 2011: Pushed out new version with following improvements:

* Facebook login! Yes, you can now have users register and log in using their Facebook account.
* New tutorial! I tried to go through everything necessary to get started with the module, see this new post.
* Transparent extending is now supported; you can start implementing your application by overriding Controller_User / Model_User with a class that extends Controller_Useradmin_User / Model_Useradmin_User.
* Bug fixes (login using email, validation messages, saving empty password)

Nov 25th 2010: Pushed out new version with following improvements:

* Now includes Kohana-Email module for sending password reset emails (users can request password request by entering an email, and they will be sent a one-time reset token via email).
* Validation code in model has been improved based on feedback from biakaveron.
* Validation code is prettier: it uses the approach from my validation-with-forms tutorial.
* Framework has been updated to v3.0.8 and minor compatibility bugs fixed.

Sep 14th 2010:

* Initial version.

### SCREENSHOT ###

![screenshot](https://github.com/mixu/useradmin/raw/master/useradmin-screen.png)

### MIGRATION from pre-Feb xxth 2011 to new schema ###

The schema had to be updated to allow for better support for multiple 3rd party providers.

Basically, the facebook_user_id field has been removed from the users table, and moved
to the user_identies table. To migrate, you need to create a row for each user with a facebook_user_id
in that table, something like:

INSERT INTO `useradmin`.`user_identities` (`user_id`, `provider`, `identity`) SELECT `users`.`id`,
"facebook", `users`.`facebook_user_id` FROM `useradmin`.`users`;

after creating the new table. You can drop `facebook_user_id` after this.

LICENCE (Simplified BSD licence)

NOTE: This licence applies to the useradmin module only, as written by Mikito
Takada (found under /modules/user/). Different licences may apply to the other
modules in this repository and the Kohana 3 core. 

-------

Copyright 2010 Mikito Takada. All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are
permitted provided that the following conditions are met:

   1. Redistributions of source code must retain the above copyright notice, this list of
      conditions and the following disclaimer.

   2. Redistributions in binary form must reproduce the above copyright notice, this list
      of conditions and the following disclaimer in the documentation and/or other materials
      provided with the distribution.

THIS SOFTWARE IS PROVIDED BY MIKITO TAKADA ``AS IS'' AND ANY EXPRESS OR IMPLIED
WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL MIKITO TAKADA OR
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

The views and conclusions contained in the software and documentation are those of the
authors and should not be interpreted as representing official policies, either expressed
or implied, of Mikito Takada.

-------

Not that I have any official policies.

### DEPENDENCIES'S LICENCES ###
(bundled in repository; don't have to find these)

I have attempted to ensure that none of the dependencies are GPL licenced,
so that they can be used in commercial applications without worrying about copyleft.

Vendors folder:

- Facebook's PHP SDK is under the Apache Licence, v.2.0 - needed for Facebook.
- LightOpenID is under MIT licence - needed for OpenID (Google, Yahoo).
- ReCaptchalib is under MIT licence - needed for ReCaptcha.

Kohana modules:

- Kohana oAuth and Auth are part of the Kohana Core.
- Kohana-email is a port of the same module which was in the Kohana v.2.x Core.
  It depends on Swiftmailer which is LGPL.

Icons:

- http://www.nouveller.com/general/free-social-media-bookmark-icon-pack-the-ever-growing-icon-set/
  Quote: "[Y]ou can use them anywhere, for anything for free with no attribution. No strings attached."

Please consult a lawyer if you need legal advice.
