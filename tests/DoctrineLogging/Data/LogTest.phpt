<?php declare(strict_types = 1);

namespace GrandMediaTests\DoctrineLogging\Data;

use GrandMedia\DoctrineLogging\Data\Action;
use GrandMedia\DoctrineLogging\Data\ChangeSet;
use GrandMedia\DoctrineLogging\Data\Log;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class LogTest extends \Tester\TestCase
{

	/** @throws \Assert\InvalidArgumentException */
	public function testBlankEntityClass(): void
	{
		Log::fromValues('userId', '', 'entityId', Action::get(Action::CREATE), ChangeSet::empty(), new \DateTimeImmutable());
	}

	/** @throws \Assert\InvalidArgumentException */
	public function testBlankEntityId(): void
	{
		Log::fromValues('userId', 'entityClass', '', Action::get(Action::CREATE), ChangeSet::empty(), new \DateTimeImmutable());
	}

}

(new LogTest())->run();
