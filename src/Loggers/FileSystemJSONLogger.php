<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\Loggers;

use GrandMedia\DoctrineLogging\Data\Log;
use Nette\IOException;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;

final class FileSystemJSONLogger implements \GrandMedia\DoctrineLogging\Loggers\Logger
{

	private string $directory;

	public function __construct(string $directory)
	{
		$this->directory = $directory;
	}

	public function log(Log $log): void
	{
		$changeSet = [];
		foreach ($log->getChangeSet()->getChanges() as $property => $change) {
			$changeSet[$property] = [
				'from' => $change->getFrom(),
				'to' => $change->getTo(),
			];
		}

		$data = [
			'createdAt' => $log->getCreatedAt()->format('Y-m-d H:i:s'),
			'userId' => $log->getUserId(),
			'entityClass' => $log->getEntityClass(),
			'entityId' => $log->getEntityId(),
			'action' => $log->getAction()->getValue(),
			'changeSet' => $changeSet,
		];

		$file = $this->getLogFile($log->getCreatedAt());

		FileSystem::createDir(\dirname($file));
		if (@file_put_contents($file, Json::encode($data) . \PHP_EOL, \FILE_APPEND | \LOCK_EX) === false) {
			throw new IOException(\sprintf('Unable to write file \'%s\'.', $file));
		}
	}

	private function getLogFile(\DateTimeInterface $date): string
	{
		return \sprintf('%s/%s/%s.log', $this->directory, $date->format('Y-m'), $date->format('Y-m-d'));
	}

}
