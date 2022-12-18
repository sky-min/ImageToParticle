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

namespace skymin\ImageParticle\particle;

use pocketmine\item\FishingRod;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use RuntimeException;
use skymin\ImageParticle\task\AsyncSendParticle;
use skymin\ImageParticle\utils\ImageTypes;
use function file_exists;
use function imagecolorat;
use function imagecreatefrombmp;
use function imagecreatefromjpeg;
use function imagecreatefrompng;
use function imagecreatefromtga;
use function imagecreatefromwebp;
use function imagesx;
use function imagesy;

final class ImageParticleAPI{
	use SingletonTrait;

	private Item $testItem;

	public const TEST_PARTICLE_TAG = 'test_image_particle';

	/**
	 * @var ImageParticle[]
	 * @phpstan-var array<string, ImageParticle>
	 */
	private array $particles = [];

	private Server $server;

	public function __construct(){
		self::setInstance($this);
		$this->setupTestItem();
		$this->server = Server::getInstance();
	}

	private function setupTestItem() : void{
		$this->testItem = VanillaItems::FISHING_ROD()
			->setNamedTag(CompoundTag::create()->setString(self::TEST_PARTICLE_TAG, ''))
			->setCustomName('§l§bImage Particle Test Item');
	}

	public function isTestItem(Item $item) : bool{
		if($item instanceof FishingRod
			&& $item->getCustomName() === $this->testItem->getCustomName()
			&& $item->getNamedTag()->getString(self::TEST_PARTICLE_TAG, '') !== ''
		) return true;
		return false;
	}

	public function createTestItem(string $name) : Item{
		$oldNbt = $this->testItem->getNamedTag();
		return $this->testItem
			->setNamedTag($oldNbt->setString(self::TEST_PARTICLE_TAG, $name))
			->setLore(['§ctest image§r: ' . $name]);
	}

	public function existsParticle(string $name) : bool{
		return isset($this->particles[$name]);
	}

	public function getParticle(string $name) : ?ImageParticle{
		return $this->particles[$name] ?? null;
	}

	/**
	 * @return  ImageParticle[]
	 * @phpstan-return  array<string, ImageParticle>
	 */
	public function getParticles() : array{
		return $this->particles;
	}

	public function registerImage(
		string $name,
		string $imageFile,
		int $imageType = ImageTypes::PNG
	) : void{
		if(isset($this->particles[$name])){
			throw new RuntimeException('already registered Particle Name');
		}
		if(!file_exists($imageFile)){
			throw new RuntimeException($imageFile . ' is not exists');
		}
		$img = match ($imageType) {
			ImageTypes::PNG => imagecreatefrompng($imageFile),
			ImageTypes::JPEG => imagecreatefromjpeg($imageFile),
			ImageTypes::WEBP => imagecreatefromwebp($imageFile),
			ImageTypes::TGA => imagecreatefromtga($imageFile),
			ImageTypes::BMP => imagecreatefrombmp($imageFile),
			default => false
		};
		if($img === false){
			throw new RuntimeException($imageFile . ' load failure');
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
				$data[(int)($x - $cx * 10)][(int)($y - $cy * 10)] = $colorat;
			}
		}
		$this->particles[$name] = new ImageParticle($name, $data);
	}

	public function sendParticle(
		string $name,
		Location $center,
		CustomParticle $customParticle,
		int $count = 0,
		float $unit = 0.1,
		//bool $asyncEncode = true
	) : void{
		$particle = $this->getParticle($name);
		if($particle === null) return;
		/*
		if($asyncEncode){
			$this->server->getAsyncPool()->submitTask(new AsyncSendParticle($particle, $center, $count, $unit));
			return;
		} */
		$vec = $center->asVector3();
		$target = $center->getWorld()->getViewersForPosition($vec);
		if($target === []) return;
		$pks = [];
		foreach($particle->encode($center, $customParticle, $count, $unit) as $particlePk){
			$pks[] = $particlePk;
		}
		$this->server->broadcastPackets($target, $pks);
	}
}
