<?php declare(strict_types = 1);

namespace GrandMediaTests\DoctrineLogging\Mocks;

use GrandMedia\DoctrineLogging\Data\Log;

final class LoggerMock implements \GrandMedia\DoctrineLogging\Loggers\Logger
{

	/**
	 * @var \GrandMedia\DoctrineLogging\Data\Log[]
	 */
	private $logs = [];

	public function log(Log $log): void
	{
		$this->logs[] = $log;
	}

	/**
	 * @return \GrandMedia\DoctrineLogging\Data\Log[]
	 */
	public function getLogs(): array
	{
		return $this->logs;
	}

}
