<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging\DateTime;

interface DateTimeProvider
{

	public function getDateTime(): \DateTimeImmutable;

}
