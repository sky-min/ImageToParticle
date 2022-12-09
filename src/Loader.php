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

namespace skymin\ImageParticle;

use skymin\ImageParticle\command\ImageParticleCmd;

use pocketmine\event\EventPriority;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use pocketmine\utils\Config;

use Symfony\Component\Filesystem\Path;
use function extension_loaded;
use function is_dir;
use function mkdir;

final class Loader extends PluginBase{

	private const IMAGE_PATH = 'image';

	private ImageParticleAPI $api;

	public function getApi() : ImageParticleAPI{
		return $this->api;
	}

	protected function onLoad() : void{
		$this->api = new ImageParticleAPI();
	}

	protected function onEnable() : void{
		if(!extension_loaded('gd')){
			throw new PluginException('Missing GD library!');
		}
		$folder = $this->getDataFolder();
		$imgPath = Path::join($folder, self::IMAGE_PATH);
		if(!is_dir($imgPath)){
			mkdir($imgPath);
		}
		foreach((new Config($folder . 'Images.yml', Config::YAML))->getAll() as $name => $data){
			$this->api->registerImage(
				name: $name,
				imageFile: Path::join($folder, self::IMAGE_PATH, $data['file']),
				imageType: ImageTypes::stringType($data['type'])
			);
		}
		$setting = new Config($folder . 'Setting.yml', Config::YAML, [
			'wait' => true,
			'wait-message' => "Image is loading.\nTry again later"
		]);
		$server = $this->getServer();
		$server->getCommandMap()->register('imageparticle', new ImageParticleCmd($this));
		if($setting->get('wait')){
			$msg = $setting->get('wait-message');
			$server->getPluginManager()->registerEvent(DataPacketReceiveEvent::class, function(DataPacketReceiveEvent $ev) use ($msg) : void{
				if(!$ev->getPacket() instanceof LoginPacket) return;
				if($this->api->getWaitList() !== []){
					$ev->getOrigin()->disconnect($msg);
				}
			}, EventPriority::MONITOR, $this);
		}
	}
}
