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
	public function encode(EulerAngle $euler, int $count = 4, float $unit = 0.1) : Generator{
		if($count < 0){
			throw new RangeException('A value greater than or equal to 0 should be obtained');
		}
		if($unit <= 0.0){
			throw new RangeException('Must be a positive value.');
		}
		$p_count = 0;
		$center = $euler->asVector3();
		$yaw = deg2rad($euler->getYaw());
		$pitch = deg2rad($euler->getPitch());
		$roll = deg2rad($euler->getRoll());
		$ysin = sin($yaw);
		$ycos = cos($yaw);
		$psin = sin($pitch);
		$pcos = cos($pitch);
		$rsin = sin($roll);
		$rcos = cos($roll);
		foreach($this->particles as $data){
			if($count === 0 || $p_count++ % $count === 0){
				$x = $data['p'][0];
				$y = $data['p'][1];
				$dx = ($y * $rsin + $x * $rcos) * $unit;
				$dy = ($y * $rcos - $x * $rsin) * $unit;
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
