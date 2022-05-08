<?php
declare(strict_types = 1);

namespace skymin\ImageParticle;

use skymin\ImageParticle\ImageParticle;
use skymin\ImageParticle\task\ImageLoadTask;

use pocketmine\plugin\PluginBase;

use skymin\data\Data;

final class Loader extends PluginBase{

	public static array $particles = [];

	protected function onEnable() : void{
		$imgPath = $this->getDataFolder() . '/image/';
		if(!is_dir($imgPath)){
			mkdir($imgPath);
		}
		$async = new ImageLoadTask(
			(new Data($this->getDataFolder() . 'Images.txt', Data::LIST))->getAll(),
			$imgPath
		);
		$this->getServer()->getAsyncPool()->submitTask($async);
		while(!$async->isFinished()){
			usleep(500);
		}
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

}