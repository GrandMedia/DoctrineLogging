<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\DI;

use Doctrine\ORM\Events;
use GrandMedia\DoctrineLogging\EntityListener;
use Kdyby\Doctrine\EntityManager;
use Nette\PhpGenerator\ClassType;

final class DoctrineLoggingExtension extends \Nette\DI\CompilerExtension implements \Kdyby\Doctrine\DI\IEntityProvider
{

	public function loadConfiguration(): void
	{
		$containerBuilder = $this->getContainerBuilder();

		$containerBuilder->addDefinition($this->prefix('entityListener'))
			->setClass(EntityListener::class);
	}

	public function afterCompile(ClassType $class): void
	{
		$builder = $this->getContainerBuilder();

		$class->getMethod('initialize')
			->addBody(
				'$this->getService(?)->getEventManager()->addEventListener(?, $this->getService(?));',
				[
					$builder->getByType(EntityManager::class),
					[
						Events::postPersist,
						Events::postUpdate,
						Events::preRemove,
						Events::postFlush,
					],
					$builder->getByType(EntityListener::class),
				]
			);
	}

	/**
	 * @return string[]
	 */
	public function getEntityMappings(): array
	{
		return [
			'GrandMedia\DoctrineLogging' => __DIR__ . '/..',
		];
	}

}
