<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\Formatters;

use GrandMedia\DoctrineLogging\Formatters\Exceptions\UnsupportedValue;

final class DateTimeFormatter implements \GrandMedia\DoctrineLogging\Formatters\Formatter
{

	/**
	 * @param mixed $value
	 */
	public function support($value): bool
	{
		return $value instanceof \DateTimeInterface;
	}

	/**
	 * @param mixed $value
	 */
	public function format($value): string
	{
		if ($value instanceof \DateTimeInterface) {
			return $value->format('Y-m-d H:i:s');
		}

		throw new UnsupportedValue();
	}

}
