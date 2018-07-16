<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\DateTime;

final class ConstantProvider implements \GrandMedia\DoctrineLogging\DateTime\DateTimeProvider
{

	/**
	 * @var \DateTimeImmutable
	 */
	private $dateTime;

	public function __construct()
	{
		$this->dateTime = new \DateTimeImmutable();
	}

	public function getDateTime(): \DateTimeImmutable
	{
		return $this->dateTime;
	}

}
