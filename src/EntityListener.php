<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;
use GrandMedia\DoctrineLogging\Data\Action;
use GrandMedia\DoctrineLogging\Data\Change;
use GrandMedia\DoctrineLogging\Data\ChangeSet;
use GrandMedia\DoctrineLogging\Data\Log;
use GrandMedia\DoctrineLogging\DateTime\DateTimeProvider;
use GrandMedia\DoctrineLogging\Formatters\Formatter;
use GrandMedia\DoctrineLogging\Loggers\Logger;
use GrandMedia\DoctrineLogging\Security\IdentityProvider;
use Nette\Security\IIdentity;

final class EntityListener
{

	/**
	 * @var \GrandMedia\DoctrineLogging\Loggers\Logger
	 */
	private $logger;

	/**
	 * @var \GrandMedia\DoctrineLogging\Security\IdentityProvider
	 */
	private $identityProvider;

	/**
	 * @var \GrandMedia\DoctrineLogging\DateTime\DateTimeProvider
	 */
	private $dateTimeProvider;

	/**
	 * @var \GrandMedia\DoctrineLogging\Formatters\Formatter[]
	 */
	private $valueFormatters = [];

	/**
	 * @var \GrandMedia\DoctrineLogging\Data\Log[]
	 */
	private $deleteLogs = [];

	public function __construct(Logger $logger, IdentityProvider $identityProvider, DateTimeProvider $dateTimeProvider)
	{
		$this->logger = $logger;
		$this->identityProvider = $identityProvider;
		$this->dateTimeProvider = $dateTimeProvider;
	}

	public function postPersist(LifecycleEventArgs $eventArgs): void
	{
		$this->logger->log($this->createLog($eventArgs, Action::create()));
	}

	public function postUpdate(LifecycleEventArgs $eventArgs): void
	{
		$log = $this->createLog($eventArgs, Action::update());

		if (!$log->getChangeSet()->isEmpty()) {
			$this->logger->log($log);
		}
	}

	public function preRemove(LifecycleEventArgs $eventArgs): void
	{
		$key = spl_object_hash($eventArgs->getEntity());
		$this->deleteLogs[$key] = $this->createLog($eventArgs, Action::delete());
	}

	public function postRemove(LifecycleEventArgs $eventArgs): void
	{
		$key = spl_object_hash($eventArgs->getEntity());
		if (isset($this->deleteLogs[$key])) {
			$this->logger->log($this->deleteLogs[$key]);
		}
	}

	public function addValueFormatter(Formatter $formatter): void
	{
		$this->valueFormatters[] = $formatter;
	}

	private function createLog(LifecycleEventArgs $eventArgs, Action $action): Log
	{
		$identity = $this->identityProvider->getIdentity();
		$entity = $eventArgs->getEntity();

		return Log::fromValues(
			$identity instanceof IIdentity ? $this->formatValue($identity->getId()) : '',
			\get_class($entity),
			$this->getEntityId($entity, $eventArgs->getEntityManager()),
			$action,
			$this->getChangeSet($eventArgs, $action),
			$this->dateTimeProvider->getDateTime()
		);
	}

	private function getChangeSet(LifecycleEventArgs $eventArgs, Action $action): ChangeSet
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();
		$entity = $eventArgs->getEntity();
		$classMetadata = $em->getClassMetadata(\get_class($entity));

		if ($action->isUpdate()) {
			$changeData = $uow->getEntityChangeSet($entity);
		} else {
			$changeData = $uow->getOriginalEntityData($entity);
			\array_walk(
				$changeData,
				function (&$value) use ($action): void {
					$value = $action->isCreate() ? ['', $value] : [$value, ''];
				}
			);
		}

		$changeSet = ChangeSet::empty();
		foreach ($changeData as $property => $data) {
			if (
				isset($classMetadata->embeddedClasses[$property]) ||
				isset($classMetadata->associationMappings[$property])
			) {
				continue;
			}

			foreach ($data as &$change) {
				if (
					\is_object($change) &&
					!$em->getMetadataFactory()->isTransient(ClassUtils::getClass($change))
				) {
					$change = $this->getEntityId($change, $em);
					continue;
				}

				$change = $this->formatValue($change);
			}
			unset($change);

			if ($data[0] !== $data[1]) {
				$changeSet->add($property, Change::fromValues($data[0], $data[1]));
			}
		}

		return $changeSet;
	}


	private function getEntityId(object $entity, ObjectManager $em): string
	{
		$identifierValues = $em->getClassMetadata(ClassUtils::getClass($entity))->getIdentifierValues($entity);

		foreach ($identifierValues as &$identifierValue) {
			$identifierValue = $this->formatValue($identifierValue);
		}
		unset($identifierValue);

		return \implode(',', $identifierValues);
	}

	/**
	 * @param mixed $value
	 */
	private function formatValue($value): string
	{
		foreach ($this->valueFormatters as $valueFormatter) {
			if ($valueFormatter->support($value)) {
				return $valueFormatter->format($value);
			}
		}

		return \is_object($value) && !\method_exists($value, '__toString') ? \serialize($value) : (string) $value;
	}

}
