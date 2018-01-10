<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging;

use Nette\Security\IIdentity;

interface IdentityProvider
{

	public function getIdentity(): ?IIdentity;

}
