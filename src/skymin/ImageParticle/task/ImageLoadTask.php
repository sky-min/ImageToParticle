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

declare(strict_types = 1);

namespace skymin\ImageParticle\task;

use skymin\ImageParticle\ImageParticle;
use skymin\ImageParticle\ImageParticleAPI;

use pocketmine\Server;
use pocketmine\scheduler\AsyncTask;

use PrefixedLogger;

use function intdiv;
use function count;
use function is_array;
use function igbinary_serialize;
use function igbinary_unserialize;

use function imagecolorat;
use function imagecreatefrompng;
use function imagesx;
use function imagesy;

final class ImageLoadTask extends AsyncTask{

	private int $count;

	private string $list;

	private PrefixedLogger $logger;

	public function __construct(
		array $list,
		private string $path
	){
		$this->list = igbinary_serialize($list);
		$this->logger = new PrefixedLogger(Server::getInstance()->getLogger(), 'ImageParticle');
	}

	public function onRun() : void{
		$list = igbinary_unserialize($this->list);
		$count = count($list);
		if($count < 1) {
			return;
		}
		$this->logger->notice("Trying to load {$count} images.");
		$this->count = $count;
		unset($count);
		$path = $this->path;
		$result = [];
		foreach($list as $name){
			$file = $path . $name . '.png';
			$img = imagecreatefrompng($file);
			if($img === false){
				continue;
			}
			$sx = imagesx($img);
			$sy = imagesy($img);
			$cx = intdiv($sx, 2);
			$cy = intdiv($sy, 2);
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
					$a = ((~((int) ($colorat >> 24))) << 1) & 0xff;
					if($a < 50){
						continue;
					}
					$data[] = [
						'c' => $colorat,
						'p' => [$x - $cx, $y - $cy]
					];
				}
			}
			$result[$name] = new ImageParticle($name, $data);
		}
		$this->setResult($result);
	}

	public function onCompletion() : void{
		$result = $this->getResult();
		if(!is_array($result)) return;
		$count = count($result);
		$this->logger->notice("{$count} of {$this->count} images loaded.");
		ImageParticleAPI::getInstance()->setParticles($result);
	}

}