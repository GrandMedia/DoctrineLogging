<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\DI;

use Doctrine\ORM\Events;
use GrandMedia\DoctrineLogging\EntityListener;
use GrandMedia\DoctrineLogging\Formatters\ArrayFormatter;
use GrandMedia\DoctrineLogging\Formatters\DateTimeFormatter;
use GrandMedia\DoctrineLogging\Formatters\EnumFormatter;
use Kdyby\Doctrine\EntityManager;
use Nette\PhpGenerator\ClassType;

final class DoctrineLoggingExtension extends \Nette\DI\CompilerExtension implements \Kdyby\Doctrine\DI\IEntityProvider
{

	/** @var mixed[] */
	public $defaults = [
		'formatters' => [
			'datetime' => true,
			'enum' => true,
			'array' => true,
		],
	];

	public function loadConfiguration(): void
	{
		$config = $this->validateConfig($this->defaults);
		$containerBuilder = $this->getContainerBuilder();

		$entityListenerDefinition = $containerBuilder->addDefinition($this->prefix('entityListener'))
			->setType(EntityListener::class);

		if ($config['formatters']['datetime']) {
			$containerBuilder->addDefinition($this->prefix('dateTimeFormatter'))
				->setType(DateTimeFormatter::class);
			$entityListenerDefinition->addSetup('addValueFormatter', [$this->prefix('@dateTimeFormatter')]);
		}
		if ($config['formatters']['enum']) {
			$containerBuilder->addDefinition($this->prefix('enumFormatter'))
				->setType(EnumFormatter::class);
			$entityListenerDefinition->addSetup('addValueFormatter', [$this->prefix('@enumFormatter')]);
		}
		if ($config['formatters']['array']) {
			$containerBuilder->addDefinition($this->prefix('arrayFormatter'))
				->setType(ArrayFormatter::class);
			$entityListenerDefinition->addSetup('addValueFormatter', [$this->prefix('@arrayFormatter')]);
		}
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
