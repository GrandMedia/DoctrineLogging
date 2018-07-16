<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\Data;

final class Action extends \Consistence\Enum\Enum
{

	public const CREATE = 'create';
	public const UPDATE = 'update';
	public const DELETE = 'delete';

	public static function create(): self
	{
		return self::get(self::CREATE);
	}

	public static function update(): self
	{
		return self::get(self::UPDATE);
	}

	public static function delete(): self
	{
		return self::get(self::DELETE);
	}

	public function isCreate(): bool
	{
		return $this->equals(self::create());
	}

	public function isUpdate(): bool
	{
		return $this->equals(self::update());
	}

	public function isDelete(): bool
	{
		return $this->equals(self::delete());
	}

}
