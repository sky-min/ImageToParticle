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

use Generator;
use RangeException;

use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\ParticleIds;

use function cos;
use function deg2rad;
use function sin;

final class ImageParticle{

	public function __construct(
		private string $name,
		private array $particles
	){}

	public function getName() : string{
		return $this->name;
	}

	/**
	 * @return Generator
	 * @phpstan-return Generator<LevelEventPacket>
	 */
	public function encode(Location $location, int $count = 4, float $unit = 0.1) : Generator{
		if($count < 0){
			throw new RangeException('A value greater than or equal to 0 should be obtained');
		}
		if($unit <= 0.0){
			throw new RangeException('Must be a positive value.');
		}
		$p_count = 0;
		$center = $location->asVector3();
		$yaw = deg2rad($location->getYaw());
		$pitch = deg2rad($location->getPitch());
		$ysin = sin($yaw);
		$ycos = cos($yaw);
		$psin = sin($pitch);
		$pcos = cos($pitch);
		foreach($this->particles as $data){
			if($count === 0 || $p_count++ % $count === 0){
				$dx = $data['p'][0] * $unit;
				$dy = $data['p'][1] * $unit;
				$dz = $dy * $psin;
				yield self::pk($center->add(
					$dz * $ysin + $dx * $ycos,
					$dy * -$pcos,
					$dz * -$ycos + $dx * $ysin
				), $data['c']);
			}
		}
	}

	private static function pk(Vector3 $pos, int $color) : LevelEventPacket{
		return LevelEventPacket::standardParticle(ParticleIds::DUST, $color, $pos);
	}

}
