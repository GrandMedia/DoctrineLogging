<?php declare(strict_types = 1);

namespace GrandMediaTests\DoctrineLogging\Loggers;

use GrandMedia\DoctrineLogging\Data\Action;
use GrandMedia\DoctrineLogging\Data\Change;
use GrandMedia\DoctrineLogging\Data\ChangeSet;
use GrandMedia\DoctrineLogging\Data\Log;
use GrandMedia\DoctrineLogging\Loggers\FileSystemJSONLogger;
use Nette\Utils\FileSystem;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class FileSystemJSONLoggerTest extends \Tester\TestCase
{

	public function testLog(): void
	{
		$logger = $this->createLogger();

		$logger->log(
			Log::fromValues(
				'userId',
				'entityClass',
				'entityId',
				Action::create(),
				ChangeSet::fromChanges(
					[
						'a' => Change::fromValues('', 'a'),
						'b' => Change::fromValues('', 'b'),
						'c' => Change::fromValues('', 'c'),
					]
				),
				new \DateTimeImmutable('2018-07-20 10:52:34')
			)
		);

		Assert::same(
			\file_get_contents(__DIR__ . '/data/fileSystemJSONLogger_1.log'),
			\file_get_contents(\TEMP_DIR . '/logs/2018-07/2018-07-20.log')
		);
	}

	private function createLogger(): FileSystemJSONLogger
	{
		$directory = \TEMP_DIR . '/logs';
		FileSystem::createDir($directory);

		return new FileSystemJSONLogger($directory);
	}

}

(new FileSystemJSONLoggerTest())->run();
