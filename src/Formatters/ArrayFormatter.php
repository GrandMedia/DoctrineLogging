<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\Formatters;

final class ArrayFormatter implements \GrandMedia\DoctrineLogging\ValueFormatter
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
			return \implode(', ', $value);
		}

		throw new UnsupportedValueException();
	}

}
