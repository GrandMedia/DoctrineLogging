<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\DI;

use GrandMedia\DoctrineLogging\EntityListener;
use Kdyby\Events\DI\EventsExtension;

final class DoctrineLoggingExtension extends \Nette\DI\CompilerExtension implements \Kdyby\Doctrine\DI\IEntityProvider
{

	public function loadConfiguration(): void
	{
		$containerBuilder = $this->getContainerBuilder();

		$containerBuilder->addDefinition($this->prefix('entityListener'))
			->setClass(EntityListener::class)
			->addTag(EventsExtension::TAG_SUBSCRIBER);
	}

	/**
	 * @return string[]
	 */
	public function getEntityMappings(): array
	{
		return [
			'Grand\DoctrineLogging' => __DIR__ . '/..',
		];
	}

}
