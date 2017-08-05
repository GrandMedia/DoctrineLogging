<?php declare(strict_types = 1);

namespace GrandMedia\DoctrineLogging;

final class Action extends \Consistence\Enum\Enum
{

	public const CREATE = 0;
	public const UPDATE = 1;
	public const DELETE = 2;

}
