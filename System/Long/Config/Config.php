<?php
/**
 * Longphp
 * Author: William Jiang
 */

namespace Long\Config;

use Long\Log\Log;
use Long\Long_Exception;

class Config
{
	//保存加载的配置项
	protected static $configItem = array();

	protected static $isLoaded = array();

	/**
	 * 自动加载配置文件
	 */
	public static function initialize()
	{
		self::loadConfig('autoload');
		$autoload = self::get('autoload_config');
		if (is_array($autoload)) {
			foreach ($autoload as $k => $configFile) {
				self::loadConfig($configFile);
			}
		}
	}


	public static function get($key = '')
	{
		if (empty($key)) return self::$configItem;
		return isset(self::$configItem[$key]) ? self::$configItem[$key] : false;
	}

	public static function set($key = '', $value = '')
	{
		if (empty($key)) return;
		self::$configItem[$key] = $value;
	}

	/**
	 * 加载配置文件
	 * @param string $file
	 * @return bool
	 */
	public static function loadConfig($file = '')
	{
		$file = str_replace('.php', '', $file) . '.php';

		if (empty($file)) {
			Long_Exception::showError('Wrong file name', 503);
		}

		$filePath = APP_PATH . DIRECTORY_SEPARATOR . 'config/' . $file;

		if (!file_exists($filePath)) {
			Long_Exception::showError('File ' . $file . 'doesn\'t exists', 503);
		} else {
			if (isset(self::$isLoaded[$file])) {
				return true;
			}

			include $filePath;

			if (empty($config) || !is_array($config)) {
				return false;
			}

			self::$isLoaded[$file] = true;
			self::$configItem = array_merge(self::$configItem, $config);
		}
		Log::writeLog('Load config file ' . $file, 'INFO');
		return true;
	}

}