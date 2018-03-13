<?php declare(strict_types = 1);

namespace GrandMediaTests\DoctrineLogging;

use GrandMedia\DoctrineLogging\Action;
use GrandMedia\DoctrineLogging\Log;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
final class LogTest extends \Tester\TestCase
{

	/** @throws \Assert\InvalidArgumentException */
	public function testBlankEntityClass(): void
	{
		new Log('userId', '', 'entityId', Action::get(Action::CREATE), 'message', new \DateTimeImmutable());
	}

	/** @throws \Assert\InvalidArgumentException */
	public function testBlankEntityId(): void
	{
		new Log('userId', 'entityClass', '', Action::get(Action::CREATE), 'message', new \DateTimeImmutable());
	}

	/** @throws \Assert\InvalidArgumentException */
	public function testBlankMessage(): void
	{
		new Log('userId', 'entityClass', 'entityId', Action::get(Action::CREATE), '', new \DateTimeImmutable());
	}

}

(new LogTest())->run();
