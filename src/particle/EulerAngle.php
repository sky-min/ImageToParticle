<?php

declare(strict_types=1);

namespace skymin\ImageParticle\particle;

use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\world\World;

final class EulerAngle extends Location{

	public float $roll;

	public function __construct(
		float $x,
		float $y,
		float $z,
		?World $world,
		float $yaw,
		float $pitch,
		float $roll
	){
		parent::__construct($x, $y, $z, $world, $yaw, $pitch);
		$this->roll = $roll;
	}

	public function getRoll() : float{
		return $this->roll;
	}

	public static function fromObject(
		Vector3 $pos,
		?World $world,
		float $yaw = 0.0,
		float $pitch = 0.0,
		float $roll = 0.0
	) : self{
		return new self(
			x: $pos->x,
			y: $pos->y,
			z: $pos->z,
			world: $pos instanceof Position ? $pos->world : $world,
			yaw: $pos instanceof Location ? $pos->getYaw() : $yaw,
			pitch: $pos instanceof Location ? $pos->getPitch() : $pitch,
			roll: $roll
		);
	}


}