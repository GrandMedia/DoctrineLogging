<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\Security;

use Nette\Security\IIdentity;
use Nette\Security\IUserStorage;

final class BasicIdentityProvider implements \GrandMedia\DoctrineLogging\IdentityProvider
{

	/** @var \Nette\Security\IUserStorage */
	private $userStorage;

	public function __construct(IUserStorage $userStorage)
	{
		$this->userStorage = $userStorage;
	}

	public function getIdentity(): ?IIdentity
	{
		return $this->userStorage->isAuthenticated() ? $this->userStorage->getIdentity() : null;
	}

}
