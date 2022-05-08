<?php
declare(strict_types = 1);

namespace skymin\ImageParticle\task;

use skymin\ImageParticle\Loader;
use skymin\ImageParticle\ImageParticle;

use pocketmine\Server;
use pocketmine\scheduler\AsyncTask;

use function file_exists;
use function intdiv;

use function imagecolorat;
use function imagecreatefrompng;
use function imagesx;
use function imagesy;

final class ImageLoadTask extends AsyncTask{

	public function __construct(
		private array $list,
		private string $path
	){}

	public function onRun() : void{
		$list = (array) $this->list;
		if($list === []) return;
		$path = $this->path;
		$result = [];
		foreach($list as $name){
			$file = $path . $name . '.png';
			$img = imagecreatefrompng($file);
			if($img === false){
				continue;
			}
			$sx = imagesx($img);
			$sy = imagesy($img);
			$cx = intdiv($sx, 2);
			$cy = intdiv($sy, 2);
			if($sx % 2 === 0){
				$cx--;
			}
			if($sy % 2 === 0){
				$cy--;
			}
			$data = [];
			for($x = 0; $x < $sx; $x++){
				for($y = 0; $y < $sy; $y++){
					$colorat = imagecolorat($img, $x, $y);
					$a = ((~((int) ($colorat >> 24))) << 1) & 0xff;
					if($a < 50){
						continue;
					}
					$data[] = [
						'c' => $colorat,
						'p' => [$x - $cx, $y - $cy]
					];
				}
			}
			$result[$name] = new ImageParticle($name, $data);
		}
		$this->setResult($result);
	}

	public function onCompletion() : void{
		Loader::$particles = $this->getResult();
	}

}