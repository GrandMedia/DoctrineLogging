<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\Formatters;

use GrandMedia\DoctrineLogging\Formatters\Exceptions\UnsupportedValue;
use Nette\Utils\Json;

final class ArrayFormatter implements \GrandMedia\DoctrineLogging\Formatters\Formatter
{

	/**
	 * @param mixed $value
	 */
	public function support($value): bool
	{
		return \is_array($value);
	}

	/**
	 * @param mixed $value
	 */
	public function format($value): string
	{
		if (\is_array($value)) {
			return Json::encode($value);
		}

		throw new UnsupportedValue();
	}

}
