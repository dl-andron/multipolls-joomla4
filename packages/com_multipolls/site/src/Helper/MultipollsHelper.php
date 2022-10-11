<?php

namespace DL\Component\Multipolls\Site\Helper;

use Joomla\CMS\Helper\ContentHelper;

\defined('_JEXEC') or die;

class MultipollsHelper extends ContentHelper
{
	/**
	 * @var string
	 *
	 */
	private static $secret_key = '721bebca5b0bf1e92f390c23ca8ec4b08c0fd7d1150452f246690d6791aae07c';
	/**
	 * @var string
	 *
	 */
	private static $secret_iv = '59690052ca0066c721243c08d8625e11';

	/**
	 * Шифрует капчу
	 *
	 * @return  string  Результат шифрования
	 *
	 */
	public static function encryptCaptcha($string)
	{
		$encrypt_method = "AES-256-CBC";

		$key = hash('sha256', self::$secret_key);
		$iv = substr(hash('sha256', self::$secret_iv), 0, 16);

		return base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
	}

	/**
	 * Дешифрует капчу
	 *
	 * @return  string  Результат дешифрования
	 *
	 */
	public static function decryptCaptcha($string)
	{
		$encrypt_method = "AES-256-CBC";

		$key = hash('sha256', self::$secret_key);
		$iv = substr(hash('sha256', self::$secret_iv), 0, 16);

		return openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	}

	/**
	 * Генерирует случайную строку
	 *
	 * @param   integer  $length    Длина строки
	 * @return  string  Сформированную строку.
	 *
	 */
	public static function generateRandomString($length = 10)
	{
		$letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($letters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++)
		{
			$randomString .= $letters[rand(0, $charactersLength - 1)];
		}
		return mb_strtolower($randomString);
	}
}
