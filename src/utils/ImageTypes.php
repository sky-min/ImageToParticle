<?php
/**
 *      _                    _
 *  ___| | ___   _ _ __ ___ (_)_ __
 * / __| |/ / | | | '_ ` _ \| | '_ \
 * \__ \   <| |_| | | | | | | | | | |
 * |___/_|\_\\__, |_| |_| |_|_|_| |_|
 *           |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the MIT License. see <https://opensource.org/licenses/MIT>.
 *
 * @author skymin
 * @link   https://github.com/sky-min
 * @license https://opensource.org/licenses/MIT MIT License
 *
 *   /\___/\
 * 　(∩`・ω・)
 * ＿/_ミつ/￣￣￣/
 * 　　＼/＿＿＿/
 *
 */


declare(strict_types=1);

namespace skymin\ImageParticle\utils;

use function strtolower;

final class ImageTypes{

	public const PNG = 0;
	public const JPEG = 1;
	public const WEBP = 2;
	public const TGA = 3;
	public const BMP = 4;

	private function __construct(){
		//NOOP
	}

	public static function stringType(string $type) : int{
		return match (strtolower($type)) {
			'png' => self::PNG,
			'jpeg' => self::JPEG,
			'webp' => self::WEBP,
			'tga' => self::TGA,
			'bmp' => self::BMP,
			default => throw new \RuntimeException($type . ' is not supported format')
		};
	}
}