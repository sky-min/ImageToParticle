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

use skymin\ImageParticle\form\ListForm;
use skymin\ImageParticle\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;

final class ImageParticleCmd extends Command implements PluginOwned{
	use PluginOwnedTrait;

	public function __construct(Loader $loader){
		parent::__construct('imageparticle', 'made by skymin', '/imageparticle [image name]', ['imgpar', 'testimg']);
		$this->setPermission('imageparticle.op');
		$this->owningPlugin = $loader;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!$sender instanceof Player || !$this->testPermission($sender)){
			return;
		}
		$sender->sendForm(new ListForm());
	}
}
