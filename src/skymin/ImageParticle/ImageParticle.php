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

	public function encode(Vector3 $center, float $yaw = 0.0, float $pitch = 0.0, int $count = 4, float $unit = 0.1) : array{
		if($count < 1){
			$count = 4;
		}
		if($unit <= 0){
			$unit = 0.1;
		}
		$pks = [];
		$p_count = 0;
		$yaw = deg2rad($yaw);
		$pitch = deg2rad($pitch);
		$ysin = sin($yaw);
		$ycos = cos($yaw);
		$psin = sin($pitch);
		$pcos = cos($pitch);
		foreach($this->particles as $key => $data){
			$p_count++;
			if($p_count === 1){
				$dx = $data['p'][0] * $unit;
				$dy = $data['p'][1] * $unit;
				$dz = $dy * $psin;
				$pks[] = self::pk($center->add(
					$dx * $ycos + $dz * $ysin,
					$dy * $pcos,
					$dx * -$ysin + $dz * $ycos,
				), $data['c']);
			}
			if($p_count >= $count){
				$p_count = 0;
			}
		}
		return $pks;
	}

	private static function pk(Vector3 $pos, int $color) : LevelEventPacket{
		return LevelEventPacket::standardParticle(ParticleIds::DUST, $color, $pos);
	}

}
