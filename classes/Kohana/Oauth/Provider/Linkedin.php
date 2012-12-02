<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth Linkedin Provider
 *
 * Documents for implementing Linkedin OAuth can be found at
 * <https://developer.linkedin.com/documents/quick-start-guide>.
 *
 * [!!] This class does not implement the Linkedin API. It is only an
 * implementation of standard OAuth with Linkedin as the service provider.
 *
 * @package    Kohana/OAuth
 * @category   Provider
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 */
class Kohana_OAuth_Provider_Linkedin extends OAuth_Provider {

	public $name = 'linkedin';

	protected $signature = 'HMAC-SHA1';

	public function url_request_token()
	{
		return 'https://api.linkedin.com/uas/oauth/requestToken';
	}

	public function url_authorize()
	{
		return 'https://api.linkedin.com/uas/oauth/authorize';
	}

	public function url_access_token()
	{
		return 'https://api.linkedin.com/uas/oauth/accessToken';
	}

	public function request_token(OAuth_Consumer $consumer, array $params = NULL)
	{
		if (empty($params))
		{
			$params = array();
		}
		$config = Kohana::$config->load('oauth.' . $this->name);
		if ($scope = Arr::get($config, 'scope'))
		{
			$params['scope'] = $scope;
		}
		return parent::request_token($consumer, $params);
	}

} // End Kohana_OAuth_Provider_Linkedin
