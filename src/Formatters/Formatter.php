<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\Formatters;

interface Formatter
{

	/**
	 * @param mixed $value
	 */
	public function support($value): bool;

	/**
	 * @param mixed $value
	 */
	public function format($value): string;

}
