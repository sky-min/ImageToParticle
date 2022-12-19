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
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use RangeException;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;

use function cos;
use function deg2rad;
use function sin;

final class ImageParticle{

	/**
	 * @param int[][] $particles
	 */
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

	public function encode(EulerAngle $euler, CustomParticle $customParticle, int $count = 0, float $unit = 0.1) : Generator{
		if($count < 0){
			throw new RangeException('A value greater than or equal to 0 should be obtained');
		}
		if($unit <= 0.0){
			throw new RangeException('Must be a positive value.');
		}
		$p_count = 0;
		$center = $euler->asVector3();
		//yaw
		$yaw = deg2rad($euler->getYaw());
		$ysin = sin($yaw);
		$ycos = cos($yaw);
		//pitch
		$pitch = deg2rad($euler->getPitch());
		$psin = sin($pitch);
		$pcos = cos($pitch);
		//roll
		$roll = deg2rad($euler->getRoll());
		$rsin = sin($roll);
		$rcos = cos($roll);
		foreach($this->particles as $data){
			if($count === 0 || $p_count++ % $count === 0){
				$x = $data[1][0] * $unit;
				$y = $data[1][1] * $unit;
				$dx = $y * $rsin + $x * $rcos;
				$dy = $y * $rcos - $x * $rsin;
				$dz = $dy * $psin;
				yield self::pk($center->add(
					$dz * $ysin + $dx * $ycos,
					$dy * -$pcos,
					$dz * -$ycos + $dx * $ysin
				), $customParticle->setColor($data[0][0], $data[0][1], $data[0][2]));
			}
		}
	}

	private static function pk(Vector3 $pos, CustomParticle $customParticle) : SpawnParticleEffectPacket{
		return SpawnParticleEffectPacket::create(
			dimensionId: DimensionIds::OVERWORLD,
			actorUniqueId: -1,
			position: $pos,
			particleName: 'skymin:custom_dust',
			molangVariablesJson: json_encode($customParticle)
		);
	}

}
