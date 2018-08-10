<?php
namespace Component\Library;

class HttpCurl
{
	private static $handle = null;
	private static $user_agent = null;
    private static $max_redirects = 8;
	private static $connect_timeout = 0.5;

    private function __clone()
    {
        //ban on cloning!!!
    }

    private function __construct()
    {
        //ban on construct!!!
    }

	public static function curlInit()
	{
		if (!empty(self::$handle))
			curl_close(self::$handle);
		self::$user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US)'
			.' AppleWebKit/532.0 (KHTML, like Gecko)'
			.' Chrome/3.0.195.32 Safari/532.0';
		self::$max_redirects = 5;
		self::$connect_timeout = 0.5;

		self::$handle = curl_init();
        curl_setopt(self::$handle, CURLOPT_USERAGENT, self::$user_agent);
        curl_setopt(self::$handle, CURLOPT_CONNECTTIMEOUT, self::$connect_timeout);
        curl_setopt(self::$handle, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt(self::$handle, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt(self::$handle, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt(self::$handle, CURLOPT_FILETIME, TRUE);
        curl_setopt(self::$handle, CURLOPT_MAXREDIRS, self::$max_redirects);
        curl_setopt(self::$handle, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt(self::$handle, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt(self::$handle, CURLOPT_HEADER, TRUE);
		curl_setopt(self::$handle, CURLOPT_NOBODY, FALSE);
	}

	public static function __callStatic($name, $params)
	{
		$method = strtoupper($name);
		$url = $params[0];
		$params = (isset($params[1]) ? $params[1] : null);

		if (empty(self::$handle))
			self::curlInit();

		curl_setopt(self::$handle, CURLOPT_PUT, FALSE);
		curl_setopt(self::$handle, CURLOPT_POST, FALSE);

		switch ($method)
		{
		case 'POST':
			curl_setopt(self::$handle, CURLOPT_POST, TRUE);
			break;
		case 'PUT':
			curl_setopt(self::$handle, CURLOPT_PUT, TRUE);
			break;
		default:
			break;
		}
		if ($params != null)
			curl_setopt(self::$handle, CURLOPT_POSTFIELDS, $params);
		curl_setopt(self::$handle, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt(self::$handle, CURLOPT_URL, $url);
		$ret = array();
		$response = curl_exec(self::$handle);
		if ($response == false)
		{
			$ret['error'] = curl_error(self::$handle);
			$ret['body'] = false;
		}
		else
		{
			list($header, $ret['body']) = explode("\r\n\r\n", $response, 2);
			$ret['header'] = self::header_handler($header);
		}
        $ret['code'] = curl_getinfo(self::$handle, CURLINFO_HTTP_CODE);
		$ret['info'] = curl_getinfo(self::$handle);

		return $ret;
	}

    private static function header_handler($header)
    {
		$arr = explode("\n", $header);
		$http_header = array('http_code' => $arr[0]);
		for ($i=1; $i<count($arr); $i++)
		{
			$pos = strpos($arr[$i], ':');
            $key = str_replace('-', '_', strtolower(substr($arr[$i], 0, $pos)));
            $value = trim(substr($arr[$i], $pos + 2));
			if (isset($http_header[$key]))
                $http_header[$key] .= ';' . $value;
			else
                $http_header[$key] = $value;
		}
        return $http_header;
    }

	public static function set_authorize($username = null, $password = null)
	{
		if (empty(self::$handle) || empty($username) || empty($password))
		{
			self::curlInit();
		}
		else
        {
            curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($handle, CURLOPT_USERPWD, "{$username}:{$password}");
        }
	}

	public static function set_header($header = null)
	{
		if (empty(self::$handle))
			self::curlInit();
		curl_setopt(self::$handle, CURLOPT_HTTPHEADER, $header);
	}

	public static function set_cookie($cookie = null)
	{
		if (empty(self::$handle))
			self::curlInit();
		if (empty($cookie) || is_string($cookie))
			$set_cookie = $cookie;
		else if (is_array($cookie))
		{
			$cookie_arr = array();
			foreach ($cookie as $key => $value)
				$cookie_arr[] = $key.'='.$value;
			$set_cookie = implode(';', $cookie_arr);
		}
		curl_setopt(self::$handle, CURLOPT_COOKIE, $set_cookie);
	}
}
?>
