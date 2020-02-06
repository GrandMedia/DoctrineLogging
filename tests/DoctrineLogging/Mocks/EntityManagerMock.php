<?php declare(strict_types = 1);

namespace GrandMediaTests\DoctrineLogging\Mocks;

use Doctrine\Common\EventManager;
use Doctrine\ORM\Query\ResultSetMapping;

final class EntityManagerMock implements \Doctrine\ORM\EntityManagerInterface
{

	/**
	 * @inheritDoc
	 */
	public function getClassMetadata($className)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getCache()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getConnection()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getExpressionBuilder()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function beginTransaction()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function transactional($func)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function commit()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function rollback()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function createQuery($dql = '')
	{
	}

	/**
	 * @inheritDoc
	 */
	public function createNamedQuery($name)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function createNativeQuery($sql, ResultSetMapping $rsm)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function createNamedNativeQuery($name)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function createQueryBuilder()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getReference($entityName, $id)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getPartialReference($entityName, $identifier)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function close()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function copy($entity, $deep = false)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function lock($entity, $lockMode, $lockVersion = null)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getEventManager()
	{
		return new EventManager();
	}

	/**
	 * @inheritDoc
	 */
	public function getConfiguration()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function isOpen()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getUnitOfWork()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getHydrator($hydrationMode)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function newHydrator($hydrationMode)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getProxyFactory()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getFilters()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function isFiltersStateClean()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function hasFilters()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function find($className, $id)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function persist($object)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function remove($object)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function merge($object)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function clear($objectName = null)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function detach($object)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function refresh($object)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function flush()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getRepository($className)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getMetadataFactory()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function initializeObject($obj)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function contains($object)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function __call($name, $arguments)
	{
		// TODO: Implement @method Mapping\ClassMetadata getClassMetadata($className)
	}

}
