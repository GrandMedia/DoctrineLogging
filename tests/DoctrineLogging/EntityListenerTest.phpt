<?php declare(strict_types = 1);

namespace GrandMediaTests\DoctrineLogging;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\UnitOfWork;
use GrandMedia\DoctrineLogging\Data\Action;
use GrandMedia\DoctrineLogging\DateTime\ConstantProvider;
use GrandMedia\DoctrineLogging\EntityListener;
use GrandMedia\DoctrineLogging\Security\BasicIdentityProvider;
use GrandMediaTests\DoctrineLogging\Entities\User;
use GrandMediaTests\DoctrineLogging\Mocks\LoggerMock;
use GrandMediaTests\DoctrineLogging\Mocks\UserStorageMock;
use Mockery\MockInterface;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
final class EntityListenerTest extends \Tester\TestCase
{

	private const DATE_TIME = '2018-07-20 10:56:40';
	private const USER_ID = 1;
	private const USER_NAME = 'george';
	private const USER_ROLE_ADMIN = 'admin';
	private const USER_ROLE_USER = 'user';

	/**
	 * @var \GrandMediaTests\DoctrineLogging\Mocks\LoggerMock
	 */
	private $logger;

	/**
	 * @var \DateTimeImmutable
	 */
	private $dateTime;

	public function testPersist(): void
	{
		$entityListener = $this->createEntityListener();
		/** @var \Doctrine\Common\Persistence\ObjectManager $objectManager */
		$objectManager = $this->createObjectManager(
			[
				'id' => self::USER_ID,
				'name' => self::USER_NAME,
				'role' => self::USER_ROLE_ADMIN,
			],
			'getOriginalEntityData'
		);

		$entityListener->postPersist(
			new LifecycleEventArgs($this->createUser(), $objectManager)
		);

		$this->checkCommon(Action::create());
		$changes = $this->logger->getLogs()[0]->getChangeSet()->getChanges();
		Assert::same('', $changes['id']->getFrom());
		Assert::same((string) self::USER_ID, $changes['id']->getTo());
		Assert::same('', $changes['name']->getFrom());
		Assert::same(self::USER_NAME, $changes['name']->getTo());
		Assert::same('', $changes['role']->getFrom());
		Assert::same(self::USER_ROLE_ADMIN, $changes['role']->getTo());
	}

	public function testUpdate(): void
	{
		$entityListener = $this->createEntityListener();
		/** @var \Doctrine\Common\Persistence\ObjectManager $objectManager */
		$objectManager = $this->createObjectManager(
			[
				'name' => [self::USER_NAME, self::USER_NAME],
				'role' => [self::USER_ROLE_ADMIN, self::USER_ROLE_USER],
			],
			'getEntityChangeSet'
		);

		$entityListener->postUpdate(
			new LifecycleEventArgs($this->createUser(), $objectManager)
		);

		$this->checkCommon(Action::update());
		$changes = $this->logger->getLogs()[0]->getChangeSet()->getChanges();
		Assert::false(isset($changes['id']));
		Assert::false(isset($changes['name']));
		Assert::same(self::USER_ROLE_ADMIN, $changes['role']->getFrom());
		Assert::same(self::USER_ROLE_USER, $changes['role']->getTo());
	}

	public function testRemove(): void
	{
		$entityListener = $this->createEntityListener();
		/** @var \Doctrine\Common\Persistence\ObjectManager $objectManager */
		$objectManager = $this->createObjectManager(
			[
				'id' => self::USER_ID,
				'name' => self::USER_NAME,
				'role' => self::USER_ROLE_ADMIN,
			],
			'getOriginalEntityData'
		);

		$eventArgs = new LifecycleEventArgs($this->createUser(), $objectManager);

		$entityListener->preRemove($eventArgs);

		Assert::same(0, \count($this->logger->getLogs()));

		$entityListener->postRemove($eventArgs);

		$this->checkCommon(Action::delete());
		$changes = $this->logger->getLogs()[0]->getChangeSet()->getChanges();
		Assert::same((string) self::USER_ID, $changes['id']->getFrom());
		Assert::same('', $changes['id']->getTo());
		Assert::same(self::USER_NAME, $changes['name']->getFrom());
		Assert::same('', $changes['name']->getTo());
		Assert::same(self::USER_ROLE_ADMIN, $changes['role']->getFrom());
		Assert::same('', $changes['role']->getTo());
	}

	protected function setUp(): void
	{
		parent::setUp();

		$this->logger = new LoggerMock();
		$this->dateTime = new \DateTimeImmutable(self::DATE_TIME);
	}

	protected function tearDown(): void
	{
		parent::tearDown();

		\Mockery::close();
	}

	private function checkCommon(Action $action): void
	{
		$logs = $this->logger->getLogs();
		Assert::same(1, \count($logs));

		$log = $logs[0];
		Assert::same(User::class, $log->getEntityClass());
		Assert::same((string) self::USER_ID, $log->getEntityId());
		Assert::same($action->getValue(), $log->getAction()->getValue());
		Assert::same($this->dateTime, $log->getCreatedAt());
	}

	/**
	 * @param mixed[] $uowMethodValues
	 */
	private function createObjectManager(array $uowMethodValues, string $uowMethod): MockInterface
	{
		$objectManager = \Mockery::mock(ObjectManager::class);

		$classMetadata = \Mockery::mock(ClassMetadata::class);
		$classMetadata->shouldReceive('getIdentifierValues')
			->andReturn([self::USER_ID]);
		$objectManager->shouldReceive('getClassMetaData')
			->andReturn($classMetadata);

		$unitOfWork = \Mockery::mock(UnitOfWork::class);
		$unitOfWork->shouldReceive($uowMethod)
			->andReturn($uowMethodValues);
		$objectManager->shouldReceive('getUnitOfWork')
			->andReturn($unitOfWork);

		return $objectManager;
	}

	private function createEntityListener(): EntityListener
	{
		return new EntityListener(
			$this->logger,
			new BasicIdentityProvider(new UserStorageMock()),
			ConstantProvider::fromDateTime($this->dateTime)
		);
	}

	private function createUser(): User
	{
		return new User(self::USER_ID, self::USER_NAME, self::USER_ROLE_ADMIN);
	}

}

(new EntityListenerTest())->run();
