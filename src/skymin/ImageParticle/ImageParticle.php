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

final class ImageParticle{

	public const LOOK_X = 0;
	public const LOOK_Y = 1;
	public const LOOK_Z = 2;

	public function __construct(
		private string $name,
		private array $particles
	){}

	public function getName() : string{
		return $this->name;
	}

	public function encode(Vector3 $center, int $count = 4, float $unit = 0.05, int $look = self::LOOK_Y) : array{
		$pks = [];
		$cx = $center->x;
		$cy = $center->y;
		$cz = $center->z;
		$p_count = 0;
		if($look === self::LOOK_X){
			foreach($this->particles as $key => $data){
				if($p_count++ === 1){
					$pks[] = $this->pk(
						new Vector3(
							$cx,
							$cy + $data['p'][1] * $unit,
							$cz + $data['p'][0] * $unit
						), $data['c']
					);
					continue;
				}
				if($p_count === $count){
					$p_count = 0;
				}
			}
		}elseif($look === self::LOOK_Z){
			foreach($this->particles as $key => $data){
				if($p_count++ === 1){
					$pks[] = $this->pk(
						new Vector3(
							$cx + $data['p'][0] * $unit,
							$cy + $data['p'][1] * $unit,
							$cz
						), $data['c']
					);
					continue;
				}
				if($p_count === $count){
					$p_count = 0;
				}
			}
		}else{
			foreach($this->particles as $key => $data){
				if($p_count++ === 1){
					$pks[] = $this->pk(
						new Vector3(
							$cx + $data['p'][0] * $unit,
							$cy,
							$cz + $data['p'][1] * $unit
						), $data['c']
					);
					continue;
				}
				if($p_count === $count){
					$p_count = 0;
				}
			}
		}
		return $pks;
	}

	private function pk(Vector3 $pos, int $color) : LevelEventPacket{
		return LevelEventPacket::standardParticle(ParticleIds::DUST, $color, $pos);
	}

}
