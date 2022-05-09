<?php
declare(strict_types = 1);

namespace skymin\ImageParticle\task;

use skymin\ImageParticle\ImageParticle;
use skymin\ImageParticle\ImageParticleAPI;

use pocketmine\Server;
use pocketmine\scheduler\AsyncTask;

use PrefixedLogger;

use function file_exists;
use function intdiv;
use function count;

use function imagecolorat;
use function imagecreatefrompng;
use function imagesx;
use function imagesy;

final class ImageLoadTask extends AsyncTask{

	private int $count;

	public function __construct(
		private array $list,
		private string $path
	){
		$this->logger = new PrefixedLogger(Server::getInstance()->getLogger(), 'ImageParticle');
	}

	public function onRun() : void{
		$list = (array) $this->list;
		$count = count($list);
		if($count < 0) return;
		$this->logger->notice("Trying to load {$count} images.");
		$this->count = $count;
		unset($count);
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
		$result = $this->getResult();
		$count = count($result);
		$this->logger->notice("{$count} of {$this->count} images loaded.");
		ImageParticleAPI::getInstance()->setParticles($result);
	}

}