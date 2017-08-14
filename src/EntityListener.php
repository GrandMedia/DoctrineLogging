<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging;

use Consistence\Enum\Enum;
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
		$entity = $eventArgs->getEntity();
		if ($entity instanceof LoggableEntity) {
			$this->logAction($entity, Action::get(Action::CREATE), $this->createMessage($eventArgs));
		}
	}

	public function postUpdate(LifecycleEventArgs $eventArgs): void
	{
		$entity = $eventArgs->getEntity();
		if ($entity instanceof LoggableEntity) {
			$message = $this->createMessage($eventArgs);
			if ($message !== '') {
				$this->logAction($entity, Action::get(Action::UPDATE), $message);
			}
		}
	}

	public function preRemove(LifecycleEventArgs $eventArgs): void
	{
		$entity = $eventArgs->getEntity();
		if ($entity instanceof LoggableEntity) {
			$this->logAction($entity, Action::get(Action::DELETE), 'success');
		}
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

	private function logAction(LoggableEntity $entity, Action $action, string $message): void
	{
		$identity = $this->userStorage->getIdentity();

		$this->logsToPersist[] = new Log(
			$identity instanceof IIdentity ? (string) $identity->getId() : '',
			$identity instanceof IdentityEntity ? $identity->getLogName() : '',
			$this->getEntityClass($entity),
			$entity->getLogId(),
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

			for ($i = 0, $s = \count($changeSet); $i < $s; $i++) {
				$change = $changeSet[$i];

				if ($change instanceof \DateTimeInterface) {
					$change = $change->format('Y-m-d H:i:s');
				} elseif ($change instanceof Enum) {
					$change = $change->getValue();
				}

				$changeSet[$i] = $change;
			}

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

	private function getEntityClass(LoggableEntity $entity): string
	{
		$parts = \explode('\\', \get_class($entity));

		return $parts[\count($parts) - 1];
	}

}
