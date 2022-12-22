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

namespace skymin\ImageParticle\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use skymin\ImageParticle\particle\ImageParticleAPI;

final class ListForm implements Form{

	/** @var string[] */
	private array $list;

	public function __construct(){
		$this->list = ImageParticleAPI::getInstance()->getParticleList();
	}

	public function jsonSerialize() : array{
		$buttons = [];
		foreach($this->list as $name){
			$buttons[] = ['text' => $name];
		}
		return [
			'type' => 'form',
			'title' => 'ImageParticle',
			'content' => 'Select the particle name to generate the test item',
			'buttons' => $buttons
 		];
	}

	public function handleResponse(Player $player, $data) : void{
		if($data === null) return;
		$player->sendForm(new CreateTestItemForm($this->list[$data]));
	}
}