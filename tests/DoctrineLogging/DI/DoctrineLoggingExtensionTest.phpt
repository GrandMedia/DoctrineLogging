<?php declare(strict_types = 1);

namespace GrandMediaTests\DoctrineLogging\DI;

use GrandMedia\DoctrineLogging\EntityListener;
use Nette\Configurator;
use Nette\DI\Container;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class DoctrineLoggingExtensionTest extends \Tester\TestCase
{

	public function testFunctionality(): void
	{
		$container = $this->createContainer(null);

		$entityListener = $container->getByType(EntityListener::class);
		Assert::true($entityListener instanceof EntityListener);
	}

	private function createContainer(?string $configFile): Container
	{
		$config = new Configurator();

		$config->setTempDirectory(TEMP_DIR);
		$config->addConfig(__DIR__ . '/config/reset.neon');
		if ($configFile !== null) {
			$config->addConfig(__DIR__ . \sprintf('/config/%s.neon', $configFile));
		}

		return $config->createContainer();
	}

}

(new DoctrineLoggingExtensionTest())->run();
