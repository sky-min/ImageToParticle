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

namespace skymin\ImageParticle;

use skymin\ImageParticle\task\AsyncSendParticle;

use pocketmine\utils\SingletonTrait;
use pocketmine\world\Position;
use pocketmine\Server;
use pocketmine\network\mcpe\compression\ZlibCompressor;
use pocketmine\network\mcpe\protocol\serializer\{PacketSerializerContext, PacketBatch};
use pocketmine\network\mcpe\convert\GlobalItemTypeDictionary;

use function count;
use function array_keys;

final class ImageParticleAPI{
	use SingletonTrait;

	/**
	 * @var ImageParticle[]
	 * @phpstan-var array<string, ImageParticle>
	 */
	private ?array $particles = null;

	private array $list = [];

	//Please don't unsing
	public function setParticles(array $particles) : void{
		if($this->particles === null){
			$this->particles = $particles;
			$this->list = array_keys($particles);
		}
	}

	public function getParticle(string $name) : ?ImageParticle{
		return $this->particles[$name] ?? null;
	}

	public function getParticleList() : array{
		return $this->list;
	}

	public function sendParticle(string $name, Position $center, float $yaw = 0.0, float $pitch = 0.0, int $count = 4, float $unit = 0.5, bool $asyncEncode = true) : void{
		$particle = $this->getParticle($name);
		if($particle === null) {
			return;
		}
		if($asyncEncode){
			Server::getInstance()->getAsyncPool()->submitTask(new AsyncSendParticle($particle, $center, $yaw, $pitch, $count, $unit));
			return;
		}
		$vec = $center->asVector3();
		$target = $center->world->getViewersForPosition($vec);
		if(count($target) < 1) {
			return;
		}
		Server::getInstance()->broadcastPackets($target, $particle->encode($vec, $yaw, $pitch, $count, $unit));
	}

}
