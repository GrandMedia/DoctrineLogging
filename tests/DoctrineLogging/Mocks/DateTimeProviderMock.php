<?php declare(strict_types = 1);

namespace GrandMediaTests\DoctrineLogging\Mocks;

final class DateTimeProviderMock implements \GrandMedia\DoctrineLogging\DateTimeProvider
{

	public function getDateTime(): \DateTimeImmutable
	{
		return new \DateTimeImmutable();
	}

}
