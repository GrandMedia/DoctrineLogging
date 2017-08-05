<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging;

interface LoggableEntity
{

	public function getLogId(): string;

}
