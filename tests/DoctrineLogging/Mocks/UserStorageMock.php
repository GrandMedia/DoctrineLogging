<?php declare(strict_types = 1);

namespace GrandMediaTests\DoctrineLogging\Mocks;

use Nette\Security\IIdentity;

final class UserStorageMock implements \Nette\Security\IUserStorage
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param bool $state
	 */
	public function setAuthenticated($state): self
	{
		return $this;
	}

	public function isAuthenticated(): bool
	{
		return false;
	}

	public function setIdentity(?IIdentity $identity = null): self
	{
		return $this;
	}

	public function getIdentity(): ?IIdentity
	{
		return null;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param string|int|\DateTimeInterface $time
	 * @param int $flags
	 */
	public function setExpiration($time, $flags = 0): self
	{
		return $this;
	}

	public function getLogoutReason(): ?int
	{
		return null;
	}

}
