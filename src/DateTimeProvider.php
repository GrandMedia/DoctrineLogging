<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging;

interface DateTimeProvider
{

	public function getDateTime(): \DateTimeInterface;

}
