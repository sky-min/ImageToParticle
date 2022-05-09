<?php
declare(strict_types = 1);

namespace skymin\ImageParticle;

use pocketmine\utils\SingletonTrait;
use pocketmine\world\Position;
use pocketmine\Server;
use pocketmine\network\mcpe\compression\ZlibCompressor;
use pocketmine\network\mcpe\protocol\serializer\{PacketSerializerContext, PacketBatch};
use pocketmine\network\mcpe\convert\GlobalItemTypeDictionary;

use function array_keys;
use function array_merge;

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
		$particles = $this->particles;
		if(!isset($particles[$name])){
			return null;
		}
		return $particles[$name];
	}

	public function getParticleList() : array{
		return $this->list;
	}

	public function sendParticle(string $name, Position $center, int $count = 4, float $unit = 0.05, int $look = ImageParticle::LOOK_Y) : void{
		$particle = $this->getParticle($name);
		if($particle === null) return;
		$vec = $center->asVector3();
		Server::getInstance()->broadcastPackets(
			$center->world->getViewersForPosition($vec),
			$particle->encode($vec, $count, $unit, $look)
		);
	}

}