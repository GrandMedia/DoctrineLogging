<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\Security;

use Nette\Security\IIdentity;
use Nette\Security\UserStorage;

final class BasicIdentityProvider implements \GrandMedia\DoctrineLogging\Security\IdentityProvider
{

	private UserStorage $userStorage;

	public function __construct(UserStorage $userStorage)
	{
		$this->userStorage = $userStorage;
	}

	public function getIdentity(): ?IIdentity
	{
		[$authenticated, $identity, $reason] = $this->userStorage->getState();

		return $authenticated ? $identity : null;
	}

}
