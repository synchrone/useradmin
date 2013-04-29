<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Useradmin custom auth user
 *
 * @package    Useradmin/Auth
 */
class Useradmin_Model_User extends Model_Auth_User {

	/**
	 * A user has many tokens and roles
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		// auth
		'roles' => array('through' => 'roles_users'),

		'user_tokens' => array(),

		// for facebook / twitter / google / yahoo identities
		'user_identities' => array('model' => 'user_identity'),
	);

	protected $_has_one= array(
	);
	
	protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
	
	protected $_updated_column = array('column' => 'modified', 'format' => 'Y-m-d H:i:s');

	/**
	 * Rules for the user model. Because the password is _always_ a hash
	 * when it's set,you need to run an additional not_empty rule in your controller
	 * to make sure you didn't hash an empty string. The password rules
	 * should be enforced outside the model or with a model helper method.
	 *
	 * @return array Rules
	 * @see Model_Auth_User::rules
	 */
	public function rules()
	{
		$parent = parent::rules();
		// fixes the max_length into min_length username value
		$parent['username'][1] = array('min_length', array(':value', 1));

        $require_email = Kohana::$config->load('useradmin.remote_auth_require_email');

        if($require_email === false && self::is_fast_registration()){
            unset($parent['email'][0]);
            unset($parent['email'][1]);
        }

		return $parent;
	}

    public function unique($field, $value)
    {
        $require_email = Kohana::$config->load('useradmin.remote_auth_require_email');

        if(self::is_fast_registration() && $field =='email' &&
            $value === null && $require_email === false
        ){
            return true; //consider email unique during social registration
        }
        return parent::unique($field,$value);
    }

    /**
     * Determines if we're in social registration mode
     * @return bool
     */
    public static function is_fast_registration()
    {
        $req = Request::current();
        return $req->controller() == 'user' &&
            $req->action() == 'provider_return';
    }

	// TODO overload filters() and add username/created_on/updated_on coluns filters

	/**
	 * Password validation for plain passwords.
	 *
	 * @param array $values
	 * @return Validation
	 * @see Model_Auth_User::get_password_validation
	 */
	public static function get_password_validation($values)
	{
		return Validation::factory($values)
			->rule('password', 'min_length', array(':value', 6))
			->rule('password_confirm', 'matches', array(':validation', ':field', 'password'));
	}
	
	/**
	 * Generates a password of given length using mt_rand.
	 * 
	 * @param int $length
	 * @return string
	 */
	public function generate_password($length = 8)
	{
		// start with a blank password
		$password = "";
		// define possible characters (does not include l, number relatively likely)
		$possible = "123456789abcdefghjkmnpqrstuvwxyz123456789";
		// add random characters to $password until $length is reached
		for ($i = 0; $i < $length; $i++) 
		{
			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$password .= $char;
		}
		return $password;
	}

	/**
	 * Transcribe name to ASCII
	 * 
	 * @param string $string
	 * @return string
	 */
	protected function transcribe($string)
	{
        $string = str_replace(' ', '.',$string);
        $string = strtr($string, array (
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'E',
            'Ж' => 'ZH',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'I',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'KH',
            'Ц' => 'TC',
            'Ч' => 'CH',
            'Ш' => 'SH',
            'Щ' => 'SHCH',
            'Ъ' => '',
            'Ы' => 'Y',
            'Ь' => '',
            'Э' => 'E',
            'Ю' => 'IU',
            'Я' => 'IA',
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'e',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'i',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'kh',
            'ц' => 'tc',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'shch',
            'ъ' => '',
            'ы' => 'y',
            'ь' => '',
            'э' => 'e',
            'ю' => 'iu',
            'я' => 'ia',
        ));
		$string = strtr($string,
			"\xA1\xAA\xBA\xBF\xC0\xC1\xC2\xC3\xC5\xC7\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1\xD2\xD3\xD4\xD5\xD8\xD9\xDA\xDB\xDD\xE0\xE1\xE2\xE3\xE5\xE7\xE8\xE9\xEA\xEB\xEC\xED\xEE\xEF\xF0\xF1\xF2\xF3\xF4\xF5\xF8\xF9\xFA\xFB\xFD\xFF\xC4\xD6\xE4\xF6",
			"_ao_AAAAACEEEEIIIIDNOOOOOUUUYaaaaaceeeeiiiidnooooouuuyyAOao"
		);

		$string = strtr($string, array("\xC6"=>"AE", "\xDC"=>"Ue", "\xDE"=>"TH", "\xDF"=>"ss",	"\xE6"=>"ae", "\xFC"=>"ue", "\xFE"=>"th"));
		$string = preg_replace("/([^a-z0-9\\.]+)/", "", strtolower($string));
		return($string);
	}

	/**
	 * Given a string, this function will try to find an unused username by appending a number.
	 * Ex. username2, username3, username4 ...
	 *
	 * @param string $base
	 */
	public function generate_username($base = '')
	{
		$base = $this->transcribe($base);
		$username = $base;
		$i = 2;
		// check for existent username
		while( $this->username_exist($username) ) 
		{
			$username = $base.$i;
			$i++;
		}
		return $username;
	}

	/**
	 * Check whether a username exists.
	 * @param string $username
	 * @return boolean
	 */
	public function username_exist($username) 
	{
		return ( (bool) $this->unique_key_exists( $username, "username") ) ;
	}

}
