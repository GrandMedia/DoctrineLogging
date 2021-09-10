<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\DateTime;

final class ConstantProvider implements \GrandMedia\DoctrineLogging\DateTime\DateTimeProvider
{

	private \DateTimeImmutable $dateTime;

	private function __construct()
	{
	}

	public static function now(): self
	{
		$provider = new self();
		$provider->dateTime = new \DateTimeImmutable();

		return $provider;
	}

	public static function fromDateTime(\DateTimeImmutable $dateTime): self
	{
		$provider = new self();
		$provider->dateTime = $dateTime;

		return $provider;
	}

	public function getDateTime(): \DateTimeImmutable
	{
		return $this->dateTime;
	}

}
