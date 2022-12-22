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

final class CreateTestItemForm implements Form{

	private const D_UNIT = 0.1;
	private const D_SIZE = 0.075;
	private const D_LIFE = 10;
	private const D_MOTION = 0;
	private const D_SPEED = 0;
	private const D_ACCELE = 0;
	private const D_ROLL = -1;

	public function __construct(private string $particleName){ }

	public function jsonSerialize() : array{
		return [
			'type' => 'custom_form',
			'title' => 'CreateTestItemForm',
			'content' => [
				[
					'type' => 'input',
					'text' => '§l§bunit §r(0 < value)',
					'default' => (string) self::D_UNIT
				],
				[
					'type' => 'input',
					'text' => '§l§bsize §r(0 < value < <= 100)',
					'default' => (string) self::D_SIZE
				],
				[
					'type' => 'input',
					'text' => '§l§blife §r(0 < value <= 1000)',
					'default' => (string) self::D_LIFE
				],
				[
					'type' => 'slider',
					'text' => '§l§bmotion_x§r',
					'min' => -1.0,
					'max' => 1.0,
					'default' => self::D_MOTION,
					'step' => 0.01
				],
				[
					'type' => 'slider',
					'text' => '§l§bmotion_y§r',
					'min' => -1.0,
					'max' => 1.0,
					'default' => self::D_MOTION,
					'step' => 0.01
				],
				[
					'type' => 'slider',
					'text' => '§l§bmotion_z§r',
					'min' => -1.0,
					'max' => 1.0,
					'default' => self::D_MOTION,
					'step' => 0.01
				],
				[
					'type' => 'input',
					'text' => '§l§bspeed §r(0 ~ 100)',
					'default' => (string) self::D_SPEED
				],
				[
					'type' => 'input',
					'text' => '§l§baccele §r(-100 ~ 100)',
					'default' => (string) self::D_ACCELE
				],
				[
					'type' => 'input',
					'text' => "§l§broll §r(If it's less than 0, it's random)",
					'default' => (string) self::D_ROLL
				]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if($data === null) return;
		$readData = static fn(int $index, float $default) : float
			=> isset($data[$index]) && is_numeric($data[$index]) ? (float) $data [$index] : $default;
		$player->getInventory()->addItem(ImageParticleAPI::getInstance()->createTestItem(
			$this->particleName,
			$readData(0, self::D_UNIT),
			$readData(1, self::D_SIZE),
			$readData(2, self::D_LIFE),
			$readData(3, self::D_MOTION),
			$readData(4, self::D_MOTION),
			$readData(5, self::D_MOTION),
			$readData(6, self::D_SPEED),
			$readData(7, self::D_ACCELE),
			$readData(8, self::D_ROLL)
		));
	}
}