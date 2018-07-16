<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\Data;

use Assert\Assertion;

class Log
{

	/**
	 * @var string
	 */
	private $userId;

	/**
	 * @var string
	 */
	private $entityClass;

	/**
	 * @var string
	 */
	private $entityId;

	/**
	 * @var \GrandMedia\DoctrineLogging\Data\Action
	 */
	private $action;

	/**
	 * @var \GrandMedia\DoctrineLogging\Data\ChangeSet
	 */
	private $changeSet;

	/**
	 * @var \DateTimeImmutable
	 */
	private $createdAt;

	private function __construct()
	{
	}

	public static function fromValues(
		string $userId,
		string $entityClass,
		string $entityId,
		Action $action,
		ChangeSet $changeSet,
		\DateTimeImmutable $createdAt
	): self
	{
		Assertion::notBlank($entityClass);
		Assertion::notBlank($entityId);

		$log = new self();
		$log->userId = $userId;
		$log->entityClass = $entityClass;
		$log->entityId = $entityId;
		$log->action = $action;
		$log->changeSet = $changeSet;
		$log->createdAt = $createdAt;

		return $log;
	}

	public function getUserId(): string
	{
		return $this->userId;
	}

	public function getEntityClass(): string
	{
		return $this->entityClass;
	}

	public function getEntityId(): string
	{
		return $this->entityId;
	}

	public function getAction(): Action
	{
		return $this->action;
	}

	public function getChangeSet(): ChangeSet
	{
		return $this->changeSet;
	}

	public function getCreatedAt(): \DateTimeImmutable
	{
		return $this->createdAt;
	}

}
