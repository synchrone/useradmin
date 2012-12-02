<?php defined('SYSPATH') or die('No direct access allowed.');

class Useradmin_Message {

    /*
     * the session key we use to store useradmin messages
     */
    public static $messages_session_key = 'useradmin_messages';

	public static function add($type, $message)
	{
		// get session messages
		$messages = Session::instance()->get(self::$messages_session_key);
		// initialize if necessary
		if (! is_array($messages))
		{
			$messages = array();
		}
		// append to messages
		$messages[$type][] = $message;
		// set messages
		Session::instance()->set(self::$messages_session_key, $messages);
	}

	public static function count()
	{
		return count(Session::instance()->get(self::$messages_session_key));
	}

	public static function output()
	{
		$str = '';
		$messages = Session::instance()->get(self::$messages_session_key);
        
		Session::instance()->delete(self::$messages_session_key);

		if ( !empty($messages) )
		{
			foreach ( $messages as $type => $messages )
			{
				foreach ($messages as $message)
				{
					$str .= '<div class="' . $type . '">' . $message . '</div>';
				}
			}
		}

		return $str;
	}
}