<?php

namespace DL\Component\Multipolls\Site\Controller;

\defined('_JEXEC') or die;

use DL\Component\Multipolls\Site\Helper\MultipollsHelper;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

class CaptchaController extends BaseController
{
	public function setCode()
	{
		echo MultipollsHelper::encryptCaptcha(MultipollsHelper::generateRandomString(6));
		jexit();
	}

	public function render()
	{
		// шифрованная капча
		$crypt_captcha = $this->input->get('code');

		// реальный текст капчи
		$str_captcha = MultipollsHelper::decryptCaptcha($crypt_captcha);

		// ширина картинки
		$width = 120;

		// высота картинки
		$height = 40;

		// размер текста
		$fontsize = 14;

		// шрифт
		$font = realpath('.').'/media/com_multipolls/fonts/open-sans-v17-cyrillic_latin-regular.ttf';

		// создаёт новое изображение
		$im = imagecreatetruecolor($width, $height);

		// устанавливает прозрачность изображения
		imagesavealpha($im, true);

		// идентификатор цвета для изображения
		$bg = imagecolorallocatealpha($im, 224, 224, 224, 90);

		// выполняет заливку цветом
		imagefill($im, 0, 0, $bg);

		$captcha = '';

		for ($i = 0; $i < strlen($str_captcha); $i++){

			$captcha .= $str_captcha[$i];

			// расстояние между символами
			$x = ($width - 20) / strlen($str_captcha) * $i + 10;

			$x = (int) $x;

			// случайное смещение
			$x = rand($x, $x + 4);

			// координата Y
			$y = $height - ( ($height - $fontsize) / 2 );

			// цвет для текущей буквы
			$curColor = imagecolorallocate( $im, rand(0, 100), rand(0, 100), rand(0, 100) );

			// случайный угол наклона
			$angle = rand(-25, 25);

			// вывод текста
			imagettftext($im, $fontsize, $angle, $x, $y, $curColor, $font, $captcha[$i]);

			$captcha = mb_strtolower($captcha);
		}

		// тип возвращаемого содержимого (картинка в формате PNG)
		header('Content-type: image/png');

		// выводим изображение
		imagepng($im);

		// очищаем память
		imagedestroy($im);

		jexit();
	}
}