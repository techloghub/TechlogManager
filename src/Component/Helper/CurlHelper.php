<?php

namespace Component\Helper;
use Component\Exception\CurlException;
use Component\Exception\ConfigException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CurlHelper {
    private $log = null;
    private $reqStartTime = null;
    public function __construct(ContainerInterface $container) {
        $this->log = $container->get("monolog.logger.curl");
    }

    private function getCurlHeaders(&$url, $options) {
        if (!$options) {
            return array();
        }
        $curlHeaders = array();
        //指定ip抓取链接
        if (isset($options['ip']) && $options['ip']) {
            $urlInfo = parse_url($url);
            $url = str_replace($urlInfo['host'], $options['ip'], $url);
            $curlHeaders[] = "Host: {$urlInfo['host']}";
        }
        return $curlHeaders;
    }

    /**
     * 上传文件
     * @param string $url 上传url
     * @param string $filePath 文件的本地绝对路径
     * @param string $options 公共的http options
     * @param string $defaultName 默认的file名参数
     * @return string $ret
     * @throws ConfigException
     */
    public function postFile($url, $filePath, $options=array(), $defaultName="file") {
        if (!$url) {
            $msg = "url is empty!";
            throw new ConfigException($msg);
        }
        if (!file_exists($filePath)) {
            throw new ConfigException("file $filePath not exists!");
        }
        $postData = array(
            "$defaultName" => "@".$filePath,
        );
        $headers = $this->getCurlHeaders($url, $options);
        return $this->postData($url, $headers, $postData);
    }

    //post数据的公共方法
    private function postData($url, $headers, $data) {
        $this->logStart($url, $headers);
        $ch = curl_init();
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        $info = curl_getinfo($ch);
        if (curl_errno($ch)) { //如果执行出错，关闭资源并报错
            $msg = "curl error: ".curl_error($ch);
            $this->log->addError($msg);
            curl_close($ch);
            throw new CurlException($msg);
        }
        if($info['http_code'] != 200)
        {
            $msg = "curl error: http_code".$info['http_code']." ret:" . $ret;
            $this->log->addError($msg);
            curl_close($ch);
            throw new CurlException($msg);
        }
        $this->logEnd($url);
        curl_close($ch);
        return $ret;
    }

    private function addParams($url, $params) {
        if (!$params || !is_array($params)) {
            return $url;
        }
        if (strpos($url, '?') > 0) {
            $url .= "&".http_build_query($params);
        } else {
            $url .= "?".http_build_query($params);
        }
        return $url;
    }

    public function get($url, $params=array(), $options=array(), $format="") {
        if (!$url) {
            $msg = "url is empty!";
            throw new ConfigException($msg);
        }
        $url = $this->addParams($url, $params);
        $headers = $this->getCurlHeaders($url, $options);
        if ($format == 'xml') {
            $headers[] = "Accept: application/xml";
        }
        $this->logStart($url, $headers);
        $ch = curl_init();
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        if (curl_errno($ch)) { //如果执行出错，关闭资源并报错
            $msg = "curl error: ".curl_error($ch);
            $this->log->addError($msg);
            curl_close($ch);
            throw new CurlException($msg);
        }
        $this->log->addDebug("url ($url) return:\n".$ret);
        $this->logEnd($url);
        curl_close($ch);
        return $ret;
    }

    public function getXml($url, $params=array(), $options=array()) {
        return $this->get($url, $params, $options, 'xml');
    }

    //直接post数据
    private function postContent($url, $content, $type, $options=array()) {
        if (!$url) {
            $msg = "url is empty!";
            throw new ConfigException($msg);
        }
        if (!$content) {
            $msg = "content is empty!";
            throw new ConfigException($msg);
        }
        $headers = $this->getCurlHeaders($url, $options);
        if ($type == "xml") {
            $headers[] = "Content-Type: application/xml; charset=utf8";
        }
        return $this->postData($url, $headers, $content);
    }

    //提交xml数据
    public function postXml($url, $xml, $options=array()) {
        return $this->postContent($url, $xml, "xml", $options);
    }

    //提交text数据
    public function postText($url, $text, $options=array()) {
        return $this->postContent($url, $text, "text", $options);
    }
    
    //post提交数据
    public function post($url, $params, $options=array()) {
        $postData = http_build_query($params);
        $headers = $this->getCurlHeaders($url, $options);
        return $this->postData($url, $headers, $postData);
    }

    //下载文件
    public function downFile($url, $localFile, $options=array()) {
        if (!$url) {
            $msg = "url is empty!";
            throw new ConfigException($msg);
        }
        $fp = fopen($localFile, "w");
        if (!$fp) {
            $msg = "$localFile can't write!";
            throw new ConfigException($msg);
        }
        $headers = $this->getCurlHeaders($url, $options);
        $this->logStart($url, $headers);
        $ch = curl_init();
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300); //最大下载时间
        curl_exec($ch);
        if (curl_errno($ch)) { //如果执行出错，关闭资源并报错
            $msg = "curl error: ".curl_error($ch);
            fclose($fp);
            $this->log->addError($msg);
            curl_close($ch);
            throw new CurlException($msg);
        }
        $this->logEnd($url);
        fclose($fp);
        curl_close($ch);
    }
    
    //https访问url（登陆用）
    public function sslGet($url, $params=array(), $options=array()) {
        if (!$url) {
            $msg = "url is empty!";
            throw new ConfigException($msg);
        }
        $url = $this->addParams($url, $params);
        $headers = $this->getCurlHeaders($url, $options);
        $this->logStart($url, $headers);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        $ret = curl_exec($ch);
        if (curl_errno($ch)) { //如果执行出错，关闭资源并报错
            $msg = "curl error: ".curl_error($ch);
            $this->log->addError($msg);
            curl_close($ch);
            throw new CurlException($msg);
        }
        $this->log->addDebug("url ($url) return:\n".$ret);
        $this->logEnd($url);
        curl_close($ch);
        return $ret;
    }

	/**
	 * 检测 url 的状态
	 *
	 * @param string $url
	 *
	 * @return url 访问正常(200)返回true，否则返回false
	 */
	public function checkUrlStatus($url)
	{
		$ch = curl_init($url); 
		curl_setopt($ch, CURLOPT_NOBODY, true); // 不取回数据 
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        #curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$result = curl_exec($ch); 
		
		$found = false; 
		if ($result !== false) { 
			// 检查http响应码是否为200 
			$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
			if ($statusCode == 200) { 
				$found = true; 
			}
			$this->log->debug('The url:'.$url.' http_code:'.$statusCode);
		} else {
				$this->log->debug('The url:'.$url.' error:'.var_export($result, true));
		}
		curl_close($ch); 
		return $found; 
	}

    private function logStart($url, $headers) {
        $this->reqStartTime = microtime(true);
        $this->log->debug("req url:".$url);
        $this->log->debug("*** curl headers ***\n".print_r($headers, true));
    }

    private function logEnd($url) {
        $diff = microtime(true) - $this->reqStartTime;
        $this->log->debug("req url finish:".$url."   spend time: ".sprintf(".3f", $diff)."s");
    }
}
