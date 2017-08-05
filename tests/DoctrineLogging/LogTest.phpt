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
		new Log('userId', 'userName', '', 'entityId', Action::get(Action::CREATE), 'message', new \DateTime());
	}

	/** @throws \Assert\InvalidArgumentException */
	public function testBlankEntityId(): void
	{
		new Log('userId', 'userName', 'entityClass', '', Action::get(Action::CREATE), 'message', new \DateTime());
	}

	/** @throws \Assert\InvalidArgumentException */
	public function testBlankMessage(): void
	{
		new Log('userId', 'userName', 'entityClass', 'entityId', Action::get(Action::CREATE), '', new \DateTime());
	}

}

(new LogTest())->run();
