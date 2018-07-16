<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Nette\Security\IIdentity;

final class EntityListener
{

	/** @var \GrandMedia\DoctrineLogging\IdentityProvider */
	private $identityProvider;

	/** @var \GrandMedia\DoctrineLogging\DateTimeProvider */
	private $dateTimeProvider;

	/** @var \GrandMedia\DoctrineLogging\Log[] */
	private $logsToPersist = [];

	/** @var \GrandMedia\DoctrineLogging\ValueFormatter[] */
	private $valueFormatters = [];

	public function __construct(IdentityProvider $identityProvider, DateTimeProvider $dateTimeProvider)
	{
		$this->identityProvider = $identityProvider;
		$this->dateTimeProvider = $dateTimeProvider;
	}

	public function postPersist(LifecycleEventArgs $eventArgs): void
	{
		$this->logAction($eventArgs, Action::get(Action::CREATE), $this->createMessage($eventArgs));
	}

	public function postUpdate(LifecycleEventArgs $eventArgs): void
	{
		$message = $this->createMessage($eventArgs);
		if ($message !== '') {
			$this->logAction($eventArgs, Action::get(Action::UPDATE), $message);
		}
	}

	public function preRemove(LifecycleEventArgs $eventArgs): void
	{
		$this->logAction($eventArgs, Action::get(Action::DELETE), 'success');
	}

	public function postFlush(PostFlushEventArgs $eventArgs): void
	{
		if (\count($this->logsToPersist)) {
			$em = $eventArgs->getEntityManager();
			$em->clear();
			foreach ($this->logsToPersist as $log) {
				$em->persist($log);
			}

			$this->logsToPersist = [];
			$em->flush();
		}
	}

	public function addValueFormatter(ValueFormatter $formatter): void
	{
		$this->valueFormatters[] = $formatter;
	}

	private function logAction(LifecycleEventArgs $eventArgs, Action $action, string $message): void
	{
		$identity = $this->identityProvider->getIdentity();
		$entity = $eventArgs->getEntity();

		if ($entity instanceof Log) {
			return;
		}

		$this->logsToPersist[] = new Log(
			$identity instanceof IIdentity ? $this->formatValue($identity->getId()) : '',
			$this->getEntityClass($entity),
			$this->getEntityId($entity, $eventArgs->getEntityManager()),
			$action,
			$message,
			$this->dateTimeProvider->getDateTime()
		);
	}

	private function createMessage(LifecycleEventArgs $eventArgs): string
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();
		$entity = $eventArgs->getEntity();
		$classMetadata = $em->getClassMetadata(\get_class($entity));
		$uow->computeChangeSet($classMetadata, $entity);

		$message = [];
		foreach ($uow->getEntityChangeSet($entity) as $property => $changeSet) {
			if (isset($classMetadata->embeddedClasses[$property])) {
				continue;
			}

			/** @var mixed[] $changeSet */
			foreach ($changeSet as &$change) {
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

			if ((string) $changeSet[0] !== (string) $changeSet[1]) {
				$message[] = \sprintf(
					'property "%s" changed from "%s" to "%s"',
					$property,
					\trim((string) \preg_replace('/\s\s+/', ' ', $changeSet[0])),
					\trim((string) \preg_replace('/\s\s+/', ' ', $changeSet[1]))
				);
			}
		}

		return \implode('\n', $message);
	}

	/**
	 * @param object $entity
	 */
	private function getEntityId($entity, EntityManager $em): string
	{
		$identifierValues = $em->getClassMetadata(ClassUtils::getClass($entity))->getIdentifierValues($entity);

		foreach ($identifierValues as &$identifierValue) {
			$identifierValue = $this->formatValue($identifierValue);
		}
		unset($identifierValue);

		return \implode(',', $identifierValues);
	}

	/**
	 * @param object $entity
	 */
	private function getEntityClass($entity): string
	{
		$parts = \explode('\\', \get_class($entity));

		return $parts[\count($parts) - 1];
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
