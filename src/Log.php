<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging;

use Assert\Assertion;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Log
{

	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $id;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $userId;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $entityClass;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $entityId;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $action;

	/**
	 * @ORM\Column(type="text")
	 * @var string
	 */
	private $message;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	private $createdAt;

	public function __construct(
		string $userId,
		string $entityClass,
		string $entityId,
		Action $action,
		string $message,
		\DateTimeInterface $createdAt
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
		$this->createdAt = \DateTime::createFromFormat(
			\DATE_ATOM,
			$createdAt->format(\DATE_ATOM),
			$createdAt->getTimezone()
		);
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getCreatedAt(): \DateTimeImmutable
	{
		return \DateTimeImmutable::createFromMutable($this->createdAt);
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
