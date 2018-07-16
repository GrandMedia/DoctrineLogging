<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\Security;

use Nette\Security\IIdentity;

interface IdentityProvider
{

	public function getIdentity(): ?IIdentity;

}
