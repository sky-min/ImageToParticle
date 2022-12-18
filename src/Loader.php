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

use pocketmine\entity\Location;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerItemUseEvent;
use skymin\ImageParticle\command\ImageParticleCmd;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use pocketmine\utils\Config;
use skymin\ImageParticle\particle\EulerAngle;
use skymin\ImageParticle\particle\ImageParticleAPI;
use skymin\ImageParticle\utils\ImageTypes;
use Symfony\Component\Filesystem\Path;
use function extension_loaded;
use function is_dir;
use function mkdir;
use function mt_rand;
use function round;

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
		$server = $this->getServer();
		$server->getCommandMap()->register($this->getName(), new ImageParticleCmd($this));
		$server->getPluginManager()->registerEvent(PlayerItemUseEvent::class, function(PlayerItemUseEvent $ev) : void{
			$item = $ev->getItem();
			if($this->api->isTestItem($item)){
				$player = $ev->getPlayer();
				$name = $item->getNamedTag()->getString(ImageParticleAPI::TEST_PARTICLE_TAG, '');
				$location = $player->getLocation();
				$centerVector = $location->addVector($player->getDirectionVector()->multiply(4));
				$yaw = $location->getYaw();
				$pitch = $location->getPitch();
				$roll = mt_rand(0, 3600) / 10;
				$center = EulerAngle::fromObject(
					$centerVector,
					$location->getWorld(),
					$yaw,
					$pitch,
					$roll
				);
				$this->api->sendParticle($name, $center);
				$player->sendPopup('§l§b' . round($yaw, 3) .  ' §f: §c' . round($pitch, 3) . ' §f: §a' . round($roll, 3));
			}
		}, EventPriority::LOWEST, $this);
	}
}
