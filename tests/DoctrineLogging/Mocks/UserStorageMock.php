<?php declare(strict_types = 1);

namespace GrandMediaTests\DoctrineLogging\Mocks;

use Nette\Security\IIdentity;

final class UserStorageMock implements \Nette\Security\IUserStorage
{

	public function setAuthenticated(bool $state): self
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

	public function setExpiration(?string $time, int $flags = 0): self
	{
		return $this;
	}

	public function getLogoutReason(): ?int
	{
		return null;
	}

}
