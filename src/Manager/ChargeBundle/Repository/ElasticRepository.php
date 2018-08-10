<?php
namespace Manager\ChargeBundle\Repository;

use Component\Library\HttpCurl;
use Component\Library\StringOpt;

class ElasticRepository
{
	private static $patterns = array(
		array(
			'pattern' => '/^get(?<index>(.*))List$/',
			'method' => 'getList'
		),
		array(
			'pattern' => '/^get(?<field>(.*))$/',
			'method' => 'getField'
		)
	);
	public static function __callStatic($name, $get_params)
	{
		$infos = array();
		foreach (self::$patterns as $pattern_infos)
		{
			if (preg_match($pattern_infos['pattern'], $name, $infos) === 1)
			{
				return self::$pattern_infos['method']($infos, $get_params);
			}
		}
	}

	private static function getList($index_infos, $get_params)
	{
		list($start, $limit, $params, $params_key) = $get_params;

		$infos = StringOpt::cameltounline($index_infos['index']);
		$infos = explode('_', $infos);
		if (empty($infos[0]) || empty($infos[1]))
			return array(-1, array());
		$index = $infos[0];
		$type = $infos[1];

		$url = 'http://localhost:9200/'.$index.'/'.$type.'/_search';
		$search_params = self::getParams($params, $params_key);
		$search_params['from'] = ($start-1)*$limit;
		$search_params['size'] = $limit;
        if (isset($params['sortby']))
		{
			$asc = (!isset($params['asc']) || $params['asc'] != 1) ?
				'desc' : 'asc';
			$search_params['sort'][] =
			   	array($params['sortby'] => array('order' => $asc));
		}
		$ret = HttpCurl::get($url, json_encode($search_params));
		if ($ret == false || $ret['body'] == false)
			return array(0, array());
		$body = json_decode($ret['body'], true);
		if ($body == false || empty($body['hits']['total']))
		{
			return array(0, array());
		}
		$totalmoney = round($body['aggregations']['totalmoney']['value'], 2);
		$body = $body['hits'];
		$ret = array();
		foreach ($body['hits'] as $hits)
		{
			$hits_ret = array('_id' => $hits['_id']);
			$ret[] = array_merge($hits_ret, $hits['_source']);
		}
		return array($body['total'], $ret, $totalmoney);
	}

	private static function getField($index_infos, $get_params)
	{
		list($index, $type, $field) = explode('_',
			StringOpt::cameltounline($index_infos['field']));
		$url = 'http://localhost:9200/'.$index.'/'.$type
			.'/_search?search_type=count';
		$params = array('aggs' => array('uniq' => array('terms' =>
			array('field' => $field, 'size' => 0))));
		$ret = HttpCurl::get($url, json_encode($params));
		if ($ret == false || $ret['body'] == false)
			return array();
		$body = json_decode($ret['body'], true);
		if ($body == false || empty($body['aggregations']['uniq']['buckets']))
			return array();
		$ret = array();
		foreach ($body['aggregations']['uniq']['buckets'] as $bucket)
			$ret[] = $bucket['key'];
		return $ret;
	}

	protected static function getParams($params, $params_key)
	{
		$ret = array();
		if (isset($params_key['like_list']))
		{
			foreach ($params_key['like_list'] as $key)
			{
				if (isset($params[$key]))
					$ret[]['query_string'][$key] = array(
						'default_field' => $key,
						'query' => '*'.self::escape($params[$key]).'*'
					);
			}
		}

        if (isset($params_key['equal_list']))
        {
			foreach ($params_key['equal_list'] as $key)
			{
				if (isset($params[$key]))
					$ret[]['term'][$key] = $params[$key];
			}
        }

        if (isset($params_key['range_list']))
        {
			foreach ($params_key['range_list'] as $key)
			{
				if (isset($params['start_'.$key]))
					$ret[]['range'][$key]['from'] = $params['start_'.$key];
				if (isset($params['end_'.$key]))
					$ret[]['range'][$key]['to'] = $params['end_'.$key];
			}
        }
		return empty($ret) ?
			array(
				'aggs' => array('totalmoney' => array('sum' => array('field' => 'money')))
			) :
			array(
				'query' => array('bool' => array('must' => $ret)),
				'aggs' => array('totalmoney' => array('sum' => array('field' => 'money')))
			);
	}

	protected static function escape($key)
	{
		$ret = '';
		for ($i=0; $i<mb_strlen($key, 'utf-8'); $i++)
		{
			if (in_array($key[$i], array('+', '-', '!', '(', ')',
		   		'{', '}', '[', ']', '^', '"', '~', '*', '?', ':', '\\', '/')))
			{
				$ret .= '\\'.$key[$i];
			}
			else if (in_array($key[$i].$key[$i+1], array('&&', '||')))
			{
				$ret .= '\\'.$key[$i].$key[$i+1];
				$i++;
			}
			else
			{
				$ret .= $key[$i];
			}
		}
		return $ret;
	}
}
?>
