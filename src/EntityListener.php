<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging;

use Consistence\Enum\Enum;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Nette\Security\IIdentity;
use Nette\Security\IUserStorage;

final class EntityListener
{

	/** @var \Nette\Security\IUserStorage */
	private $userStorage;

	/** @var \GrandMedia\DoctrineLogging\DateTimeProvider */
	private $dateTimeProvider;

	/** @var \GrandMedia\DoctrineLogging\Log[] */
	private $logsToPersist = [];

	public function __construct(IUserStorage $userStorage, DateTimeProvider $dateTimeProvider)
	{
		$this->userStorage = $userStorage;
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

	private function logAction(LifecycleEventArgs $eventArgs, Action $action, string $message): void
	{
		$identity = $this->userStorage->getIdentity();
		$entity = $eventArgs->getEntity();

		if ($entity instanceof Log) {
			return;
		}

		$this->logsToPersist[] = new Log(
			$identity instanceof IIdentity ? (string) $identity->getId() : '',
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
				if ($change instanceof \DateTimeInterface) {
					$change = $change->format('Y-m-d H:i:s');
				} elseif ($change instanceof Enum) {
					$change = $change->getValue();
				} elseif (
					\is_object($change) &&
					!$em->getMetadataFactory()->isTransient(ClassUtils::getClass($change))
				) {
					$change = \implode(
						',',
						$em->getClassMetadata(ClassUtils::getClass($change))->getIdentifierValues($change)
					);
				}
			}
			unset($change);

			if ((string) $changeSet[0] !== (string) $changeSet[1]) {
				$message[] = \sprintf(
					'property "%s" changed from "%s" to "%s"',
					$property,
					!\is_array($changeSet[0]) ? \trim(\preg_replace('/\s\s+/', ' ', $changeSet[0])) : 'an array',
					!\is_array($changeSet[1]) ? \trim(\preg_replace('/\s\s+/', ' ', $changeSet[1])) : 'an array'
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
		return \implode(',', $em->getClassMetadata(\get_class($entity))->getIdentifier());
	}

	/**
	 * @param object $entity
	 */
	private function getEntityClass($entity): string
	{
		$parts = \explode('\\', \get_class($entity));

		return $parts[\count($parts) - 1];
	}

}
