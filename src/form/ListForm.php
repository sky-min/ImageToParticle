<?php

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