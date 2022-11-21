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

namespace skymin\ImageParticle\command;

use skymin\FormLib\CustomForm;
use skymin\FormLib\element\{Dropdown, Input};
use skymin\ImageParticle\ImageParticleAPI;
use skymin\ImageParticle\Loader;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\scheduler\ClosureTask;

use function explode;
use function is_numeric;

final class ImageParticleCmd extends Command implements PluginOwned{
	use PluginOwnedTrait;

	public function __construct(Loader $loader){
		parent::__construct('imageparticle', 'made by skymin', '/imageparticle', ['imgpar']);
		$this->setPermission('imageparticle.op');
		$this->owningPlugin = $loader;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!$sender instanceof Player || !$this->testPermission($sender)){
			return;
		}
		$pos = $sender->getLocation();
		$posdefault = "$pos->x:$pos->y:$pos->z";
		$sender->sendForm(new CustomForm(
			'ImageParticle',
			[
				new Dropdown('name', ImageParticleAPI::getInstance()->getParticleList()),
				new Input('pos', $posdefault, 'x:y:z', Input::TYPE_STRING, true),
				new Input('yaw', '0.0', '', Input::TYPE_FLOAT, true),
				new Input('pitch', '0.0', '', Input::TYPE_FLOAT, true),
				new Input('count', '4', '', Input::TYPE_INT, true),
				new Input('unit', '0.5', '', Input::TYPE_FLOAT, true)
			],
			function(Player $player, $data) use ($pos, $posdefault) : void{
				$newpos = $pos;
				if($data[1] !== $posdefault){
					$explode = explode(':', $data[1], 3);
					if(
						isset($explode[0], $explode[1], $explode[2]) && is_numeric($explode[0]) && is_numeric($explode[1]) && is_numeric($explode[2])
					){
						$newpos = new Location((float) $explode[0], (float) $explode[1], (float) $explode[2], $pos->getWorld(), $data[2], $data[3]);
					}
				}
				$this->owningPlugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($newpos, $data) : void{
					ImageParticleAPI::getInstance()->sendParticle($data[0], $newpos, $data[4], $data[5]);
				}), 4);
			}
		));
	}

}
