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
	}

}
