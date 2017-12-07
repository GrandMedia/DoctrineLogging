<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging;

interface ValueFormatter
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
