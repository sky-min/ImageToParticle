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

namespace skymin\ImageParticle\task;

use PrefixedLogger;

use skymin\ImageParticle\ImageParticleAPI;
use skymin\ImageParticle\ImageTypes;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

use function file_exists;
use function is_array;
use function imagecolorat;
use function imagecreatefrombmp;
use function imagecreatefromjpeg;
use function imagecreatefrompng;
use function imagecreatefromtga;
use function imagecreatefromwebp;
use function imagesx;
use function imagesy;

final class ImageLoadTask extends AsyncTask{

	private PrefixedLogger $logger;

	public function __construct(
		private string $fileName,
		private int $type
	){
		$this->logger = new PrefixedLogger(Server::getInstance()->getLogger(), 'ImageParticle');
	}

	public function onRun() : void{
		$this->logger->debug('Starting to load ' . $this->fileName);
		if(!file_exists($this->fileName)){
			$this->logger->warning($this->fileName . ' is not exists');
			return;
		}
		$img = match ($this->type) {
			ImageTypes::PNG => imagecreatefrompng($this->fileName),
			ImageTypes::JPEG => imagecreatefromjpeg($this->fileName),
			ImageTypes::WEBP => imagecreatefromwebp($this->fileName),
			ImageTypes::TGA => imagecreatefromtga($this->fileName),
			ImageTypes::BMP => imagecreatefrombmp($this->fileName),
			default => false
		};
		if($img === false){
			$this->logger->warning($this->fileName . ' load failure');
			return;
		}
		$sx = imagesx($img);
		$sy = imagesy($img);
		$cx = $sx / 2 - 0.5;
		$cy = $sy / 2 - 0.5;
		if($sx % 2 === 0){
			$cx--;
		}
		if($sy % 2 === 0){
			$cy--;
		}
		$data = [];
		for($y = 0; $y < $sy; $y++){
			for($x = 0; $x < $sx; $x++){
				$colorat = imagecolorat($img, $x, $y);
				$a = ((~($colorat >> 24)) << 1) & 0xff;
				if($a < 50){
					continue;
				}
				$data[] = [
					'c' => $colorat,
					'p' => [$x - $cx, $y - $cy]
				];
			}
		}
		$this->logger->debug($this->fileName . ' load complete');
		$this->setResult($data);
	}

	public function onCompletion() : void{
		$result = $this->getResult();
		if(!is_array($result)){
			ImageParticleAPI::getInstance()->failImageLoad(spl_object_id($this));
			return;
		}
		ImageParticleAPI::getInstance()->setParticle(
			id: spl_object_id($this),
			data: $result
		);
	}

}