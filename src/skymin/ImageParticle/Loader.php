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

use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;

use skymin\ImageParticle\command\ImageParticleCmd;
use skymin\ImageParticle\task\ImageLoadTask;

use skymin\data\Data;

use function mkdir;
use function is_dir;
use function usleep;

final class Loader extends PluginBase{

	private ImageParticleAPI $api;

	protected function onEnable() : void{
		if(!extension_loaded('gd')){
			throw new PluginException("Missing GD library!");
		}
		$folder = $this->getDataFolder();
		$imgPath = $folder . 'image/';
		if(!is_dir($imgPath)){
			mkdir($imgPath);
		}
		$async = new ImageLoadTask(
			(new Data($folder . 'Images.txt', Data::LIST))->getAll(),
			$imgPath
		);
		$server = $this->getServer();
		$server->getAsyncPool()->submitTask($async);
		$server->getCommandMap()->register('imageparticle', new ImageParticleCmd($this));
		$this->api = ImageParticleAPI::getInstance();
		while(!$async->isFinished()){
			usleep(250);
		}
	}

	public function getApi() : ImageParticleAPI{
		return $this->api;
	}

}
