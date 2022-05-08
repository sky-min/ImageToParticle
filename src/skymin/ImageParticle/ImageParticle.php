<?php
declare(strict_types = 1);

namespace skymin\ImageParticle;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\ParticleIds;

use function sin;
use function cos;
use function deg2rad;

final class ImageParticle{

	private const TYPE_DOWN = 0;
	private const TYPE_UP = 1;

	public function __construct(
		private string $name,
		private array $particles
	){}

	public function encode(Vector3 $center, int $count = 4, float $angle = 0.0, float $unit = 0.1) : array{
		$pks = [];
		$cx = $center->x;
		$cy = $center->y;
		$cz = $center->z;
		$radian = deg2rad($angle);
		$p_count = 0;
		$cc = cos($radian);
		$ss = sin(-$radian);
		foreach($this->particles as $key => $data){
			if($p_count++ === 1){
				$dx = $data['p'][0] * $unit;
				$dz = $data['p'][1] * $unit;
				$pks[] = $this->pk(
					new Vector3(
						$cx + ($dz*$ss) + ($dx*$cc),
						$cy,
						$cz + ($dz*$cc) - ($dx*$ss)
					), $data['c']
				);
				continue;
			}
			if($p_count === $count){
				$p_count = 0;
			}
		}
		return $pks;
	}

	private function pk(Vector3 $pos, int $color) : LevelEventPacket{
		return LevelEventPacket::standardParticle(ParticleIds::DUST, $color, $pos);
	}

}