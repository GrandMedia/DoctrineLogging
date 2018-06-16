<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging;

use Assert\Assertion;

class Log
{

	/**
	 * @var int
	 */
	private $id;

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
	 * @var int
	 */
	private $action;

	/**
	 * @var string
	 */
	private $message;

	/**
	 * @var \DateTimeImmutable
	 */
	private $createdAt;

	public function __construct(
		string $userId,
		string $entityClass,
		string $entityId,
		Action $action,
		string $message,
		\DateTimeImmutable $createdAt
	)
	{
		Assertion::notBlank($entityClass);
		Assertion::notBlank($entityId);
		Assertion::notBlank($message);

		$this->userId = $userId;
		$this->entityClass = $entityClass;
		$this->entityId = $entityId;
		$this->action = $action->getValue();
		$this->message = $message;
		$this->createdAt = $createdAt;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getCreatedAt(): \DateTimeImmutable
	{
		return $this->createdAt;
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
		return Action::get($this->action);
	}

	public function getMessage(): string
	{
		return $this->message;
	}

}
