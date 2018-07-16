<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\Formatters;

use Consistence\Enum\Enum;
use GrandMedia\DoctrineLogging\Formatters\Exceptions\UnsupportedValue;

final class EnumFormatter implements \GrandMedia\DoctrineLogging\Formatters\Formatter
{

	/**
	 * @param mixed $value
	 */
	public function support($value): bool
	{
		return $value instanceof Enum;
	}

	/**
	 * @param mixed $value
	 */
	public function format($value): string
	{
		if ($value instanceof Enum) {
			return (string) $value->getValue();
		}

		throw new UnsupportedValue();
	}

}
