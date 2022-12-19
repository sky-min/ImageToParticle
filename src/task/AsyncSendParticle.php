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


use pocketmine\math\Vector3;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\world\World;
use skymin\ImageParticle\particle\CustomParticle;
use skymin\ImageParticle\particle\EulerAngle;
use skymin\ImageParticle\particle\ImageParticle;
use function igbinary_serialize;
use function igbinary_unserialize;

class AsyncSendParticle extends AsyncTask{

	private string $particle;

	private string $center;

	private string $custom;

	private float $yaw;

	private float $pitch;

	private float $roll;

	public function __construct(
		ImageParticle $particle,
		EulerAngle $center,
		CustomParticle $custom,
		private int $count,
		private float $unit
	){
		$this->yaw = $center->getYaw();
		$this->pitch = $center->getPitch();
		$this->roll = $center->getRoll();
		$this->storeLocal('world', $center->world);
		$this->particle = igbinary_serialize($particle);
		$this->center = igbinary_serialize($center->asVector3());
		$this->custom = igbinary_serialize($custom);
	}

	public function onRun() : void{
		/** @var ImageParticle $particle */
		$particle = igbinary_unserialize($this->particle);
		/** @var Vector3 $center */
		$center = igbinary_unserialize($this->center);
		/** @var CustomParticle $custom */
		$custom = igbinary_unserialize($this->custom);
		$result = [];
		foreach($particle->encode(
			EulerAngle::fromObject($center, null, $this->yaw, $this->pitch, $this->roll),
			$custom,
			$this->count,
			$this->unit
		) as $pk){
			$result[] = $pk;
		}
		$this->setResult($result);
	}

	public function onCompletion() : void{
		/** @var World $world */
		$world = $this->fetchLocal('world');
		if(!$world->isLoaded()) return;
		/** @var Vector3 $center */
		$center = igbinary_unserialize($this->center);
		$target = $world->getViewersForPosition($center);
		if($target === []) return;
		$particles = $this->getResult();
		Server::getInstance()->broadcastPackets($target, $particles);
	}

}
