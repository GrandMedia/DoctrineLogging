<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\Data;

use Assert\Assertion;

final class Change
{

	private string $from;
	private string $to;

	private function __construct()
	{
	}

	public static function fromValues(string $from, string $to): self
	{
		Assertion::notSame($from, $to);

		$change = new self();
		$change->from = $from;
		$change->to = $to;

		return $change;
	}

	public function getFrom(): string
	{
		return $this->from;
	}

	public function getTo(): string
	{
		return $this->to;
	}

}
