<?php

declare(strict_types=1);

namespace skymin\ImageParticle\particle;

use InvalidArgumentException;
use JsonSerializable;
use pocketmine\color\Color;
use pocketmine\math\Vector3;

final class CustomParticle implements JsonSerializable{

	private const VAR_TYPE_ARRAY = 'member_array';
	private const VAR_TYPE_FLOAT = 'float';

	private const VAR_BASE = 'variable.';
	private const VAR_COLOR = self::VAR_BASE . 'color';
	private const VAR_MOTION = self::VAR_BASE . 'm_';
	private const VAR_MOTION_X = self::VAR_MOTION . 'x';
	private const VAR_MOTION_Y = self::VAR_MOTION . 'y';
	private const VAR_MOTION_Z = self::VAR_MOTION . 'z';
	private const VAR_SPEED = self::VAR_BASE . 'speed';
	private const VAR_ACCELE = self::VAR_BASE . 'accele';
	private const VAR_SIZE = self::VAR_BASE . 'size';
	private const VAR_LIFE = self::VAR_BASE . 'life';

	private const RGB_MAX = 255;

	private const VECTOR_MAX_SIZE = 1;
	private const VECTOR_MIN_SIZE = -1;

	private const SPEED_MAX = 100;
	private const SPEED_MIN = 0;

	private const ACCELE_MAX = 100;
	private const ACCELE_MIN = -100;

	private const SIZE_MAX = 100;
	private const SIZE_CUT = 0;

	private const LIFE_MAX = 1000;
	private const LIFE_MIN = 0;

	private int $colorCode = 0;
	private Color $color;

	/**
	 * @param float        $size   Greater than 0 and less than or equal to 100
	 * @param float        $life   0 ~ 1000 | 0 is infinite life
	 * @param null|Vector3 $motion If null, it is automatically set to 0. Don`t exceed the value of 1 for the values x, y, and z of the Vector.
	 * @param float        $speed  0 ~ 100
	 * @param float        $accele -100 ~ 100
	 *
	 * @throws InvalidArgumentException If less than the minimum or greater than the maximum
	 */
	public function __construct(
		// shape
		private float $size = 0.075,
		// life
		private float $life = 0.0,
		// motion
		private ?Vector3 $motion = null,
		private float $speed = 0.0,
		private float $accele = 0.0
	){
		if($this->motion === null){
			$this->motion = new Vector3(0, 0, 0);
		}
		$this->checkVector();
		$this->checkSpeed();
		$this->checkAccele();
		$this->checkSize();
		$this->checkLife();
		$this->color = new Color(0, 0, 0);
	}


	public function setColor(int $color) : self{
		if($this->colorCode === $color) return $this;
		$this->colorCode = $color;
		$this->color = Color::fromRGB($color);
		return $this;
	}


	private function checkVector() : void{
		$x = $this->motion->getX();
		$y = $this->motion->getY();
		$z = $this->motion->getZ();
		if(
			$x > self::VECTOR_MAX_SIZE ||
			$y > self::VECTOR_MAX_SIZE ||
			$z > self::VECTOR_MAX_SIZE ||
			$x < self::VECTOR_MIN_SIZE ||
			$y < self::VECTOR_MIN_SIZE ||
			$z < self::VECTOR_MIN_SIZE
		){
			throw new InvalidArgumentException('Vector size must be between ' . self::VECTOR_MAX_SIZE . ' and ' . self::VECTOR_MIN_SIZE);
		}
	}

	private function checkSpeed() : void{
		if($this->speed > self::SPEED_MAX || $this->speed < self::SPEED_MIN){
			throw new InvalidArgumentException('speed must be between ' . self::SPEED_MIN . ' and ' . self::SPEED_MAX);
		}
	}

	private function checkAccele() : void{
		if($this->accele > self::ACCELE_MAX || $this->accele < self::ACCELE_MIN){
			throw new InvalidArgumentException('accele must be between ' . self::ACCELE_MIN . ' and ' . self::ACCELE_MAX);
		}
	}

	private function checkSize() : void{
		if($this->size > self::SIZE_MAX || $this->size <= self::SIZE_CUT){
			throw new InvalidArgumentException('size must be greater than ' .  self::SIZE_CUT . ' and less than or equal to ' . self::SIZE_MAX);
		}
	}

	private function checkLife() : void{
		if($this->life > self::LIFE_MAX || $this->life < self::LIFE_MIN){
			throw new InvalidArgumentException('accele must be between ' . self::LIFE_MIN . ' and ' . self::LIFE_MAX);
		}
	}

	public function jsonSerialize() : array{
		return [
			[
				'name' => self::VAR_COLOR,
				'value' => [
					'type' => self::VAR_TYPE_ARRAY,
					'value' => [
						[
							'name' => '.r',
							'value' => [
								'type' => self::VAR_TYPE_FLOAT,
								'value' => $this->color->getR() / self::RGB_MAX
							]
						],
						[
							'name' => '.g',
							'value' => [
								'type' => self::VAR_TYPE_FLOAT,
								'value' => $this->color->getG() / self::RGB_MAX
							]
						],
						[
							'name' => '.b',
							'value' => [
								'type' => self::VAR_TYPE_FLOAT,
								'value' => $this->color->getB() / self::RGB_MAX
							]
						]
					]
				]
			],
			[
				'name' => self::VAR_MOTION_X,
				'value' => [
					'type' => self::VAR_TYPE_FLOAT,
					'value' => $this->motion->getX()
				]
			],
			[
				'name' => self::VAR_MOTION_Y,
				'value' => [
					'type' => self::VAR_TYPE_FLOAT,
					'value' => $this->motion->getY()
				]
			],
			[
				'name' => self::VAR_MOTION_Z,
				'value' => [
					'type' => self::VAR_TYPE_FLOAT,
					'value' => $this->motion->getZ()
				]
			],
			[
				'name' => self::VAR_SPEED,
				'value' => [
					'type' => self::VAR_TYPE_FLOAT,
					'value' => $this->speed / self::SPEED_MAX
				]
			],
			[
				'name' => self::VAR_ACCELE,
				'value' => [
					'type' => self::VAR_TYPE_FLOAT,
					'value' => $this->accele / self::ACCELE_MAX
				]
			],
			[
				'name' => self::VAR_SIZE,
				'value' => [
					'type' => self::VAR_TYPE_FLOAT,
					'value' => $this->size / self::SIZE_MAX
				]
			],
			[
				'name' => self::VAR_LIFE,
				'value' => [
					'type' => self::VAR_TYPE_FLOAT,
					'value' => $this->life / self::LIFE_MAX
				]
			]
		];
	}

}