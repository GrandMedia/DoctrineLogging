<?php declare(strict_types = 1);

namespace GrandMediaTests\DoctrineLogging\Mocks;

use Nette\Security\IIdentity;

final class UserStorageMock implements \Nette\Security\UserStorage
{

	public function saveAuthentication(IIdentity $identity): void
	{
	}

	public function clearAuthentication(bool $clearIdentity): void
	{
	}

	/**
	 * @return array{bool, ?\Nette\Security\IIdentity, ?int}
	 */
	public function getState(): array
	{
		return [false, null, null];
	}

	public function setExpiration(?string $expire, bool $clearIdentity): void
	{
	}

}
