<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\Data;

final class ChangeSet
{

	/** @var \GrandMedia\DoctrineLogging\Data\Change[] */
	private array $changes = [];

	private function __construct()
	{
	}

	public static function empty(): self
	{
		return new self();
	}

	/**
	 * @param \GrandMedia\DoctrineLogging\Data\Change[] $changes
	 */
	public static function fromChanges(array $changes): self
	{
		$changeSet = self::empty();
		foreach ($changes as $property => $change) {
			$changeSet->add($property, $change);
		}

		return $changeSet;
	}

	public function add(string $property, Change $change): void
	{
		$this->changes[$property] = $change;
	}

	/**
	 * @return \GrandMedia\DoctrineLogging\Data\Change[]
	 */
	public function getChanges(): array
	{
		return $this->changes;
	}

	public function isEmpty(): bool
	{
		return \count($this->changes) === 0;
	}

}
