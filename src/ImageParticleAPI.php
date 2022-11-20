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

namespace skymin\ImageParticle;

use skymin\ImageParticle\task\AsyncSendParticle;
use skymin\ImageParticle\task\ImageLoadTask;

use pocketmine\entity\Location;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

use function count;

final class ImageParticleAPI{
	use SingletonTrait;

	/**
	 * @var ImageParticle[]
	 * @phpstan-var array<string, ImageParticle>
	 */
	private array $particles = [];

	/** @var string[] */
	private array $list = [];

	private Server $server;

	public function __construct(){
		$this->server = Server::getInstance();
	}

	//Please don't use
	public function setParticles(string $key, ImageParticle $particle) : void{
		$this->particles[$key] = $particle;
	}

	public function getParticleList() : array{
		return $this->list;
	}

	public function registerImage(string $name, string $imageFile, int $imageType = ImageTypes::PNG) : void{
		if(isset($this->list[$name])){
			throw new \RuntimeException('already registered Particle Name');
		}
		$this->list[] = $name;
		$this->server->getAsyncPool()->submitTask(new ImageLoadTask($name, $imageFile, $imageType));
	}

	//Standard
	public function sendParticle(
		string $name,
		Location $center,
		int $count = 4,
		float $unit = 0.5,
		bool $asyncEncode = true
	) : void{
		$particle = $this->getParticle($name);
		if($particle === null){
			return;
		}
		if($asyncEncode){
			$this->server->getAsyncPool()->submitTask(new AsyncSendParticle($particle, $center, $count, $unit));
			return;
		}
		$vec = $center->asVector3();
		$target = $center->world->getViewersForPosition($vec);
		if(count($target) === 1){
			return;
		}
		$pks = [];
		foreach($particle->encode($center, $count, $unit) as $particlePk){
			$pks[] = $particlePk;
		}
		$this->server->broadcastPackets($target, $pks);
	}

	public function getParticle(string $name) : ?ImageParticle{
		return $this->particles[$name] ?? null;
	}

}
