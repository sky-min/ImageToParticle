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

use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\scheduler\AsyncTask;

use function count;
use function igbinary_serialize;
use function igbinary_unserialize;

final class AsyncSendParticle extends AsyncTask{

	private string $particle;
	
	private string $center;

	public function __construct(
		ImageParticle $particle,
		Position $center,
		private float $yaw,
		private float $pitch,
		private int $count,
		private float $unit
	){
		$this->particle = igbinary_serialize($particle);
		$this->storeLocal('world', $center->world);
		$this->center = igbinary_serialize($center->asVector3());
	}

	public function onRun() : void{
		$particle = igbinary_unserialize($this->particle);
		$center = igbinary_unserialize($this->center);
		$result = $particle->encode($center, $this->yaw, $this->pitch, $this->count, $this->unit);
		$this->setResult($result);
	}

	public function onCompletion() : void{
		$world = $this->fetchLocal('world');
		if(!$world->isLoaded()) return;
		$target = $world->getViewersForPosition($center);
		if(count($target) < 1) return;
		$particles = $this->getResult();
		$center = igbinary_unserialize($this->center);
		Server::getInstance()->broadcastPackets($target, $particles);
	}

}