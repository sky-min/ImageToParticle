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

	/**
	 * @var string[]
	 * @phpstan-var array<int, string>
	 */
	private array $loadWaitingList = [];

	public function __construct(){
		self::setInstance($this);
		$this->server = Server::getInstance();
	}

	//Please don't use
	public function setParticle(int $id, array $data) : void{
		$name = $this->loadWaitingList[$id];
		unset($this->loadWaitingList[$id]);
		var_dump($this->loadWaitingList);
		$this->particles[$name] = new ImageParticle($name, $data);
	}

	public function getParticle(string $name) : ?ImageParticle{
		return $this->particles[$name] ?? null;
	}

	public function getParticleList() : array{
		return $this->list;
	}

	public function registerImage(string $name, string $imageFile, int $imageType = ImageTypes::PNG) : void{
		if(isset($this->list[$name])){
			throw new \RuntimeException('already registered Particle Name');
		}
		$this->list[] = $name;
		$task = new ImageLoadTask($name, $imageFile, $imageType);
		$this->loadWaitingList[spl_object_id($task)] = $name;
		$this->server->getAsyncPool()->submitTask($task);
	}

	public function sendParticle(
		string $name,
		Location $center,
		int $count = 4,
		float $unit = 0.5,
		bool $asyncEncode = true
	) : void{
		$particle = $this->getParticle($name);
		if($particle === null) return;
		if($asyncEncode){
			$this->server->getAsyncPool()->submitTask(new AsyncSendParticle($particle, $center, $count, $unit));
			return;
		}
		$vec = $center->asVector3();
		$target = $center->world->getViewersForPosition($vec);
		if(count($target) === 1) return;
		$pks = [];
		foreach($particle->encode($center, $count, $unit) as $particlePk){
			$pks[] = $particlePk;
		}
		$this->server->broadcastPackets($target, $pks);
	}

}
