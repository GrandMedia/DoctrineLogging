<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\Loggers;

use GrandMedia\DoctrineLogging\Data\Log;

interface Logger
{

	public function log(Log $log): void;

}
