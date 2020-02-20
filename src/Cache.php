<?php


namespace Vitrex\Cache;


use Vitrex\Cache\Adapter\Adapter;

/**
 * Class Cache
 *
 * @package Vitrex\Cache
 */
class Cache
{
	
	/**
	 * @var Adapter
	 */
	protected Adapter $adapter;
	
	/**
	 * Cache constructor.
	 *
	 * @param Adapter|null $adapter
	 */
	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
	}
	
	/**
	 * @param string $id
	 * @return mixed
	 */
	public function load(string $id)
	{
		return $this->adapter->load($id);
	}
	
	/**
	 * @param string $id
	 * @param mixed $data
	 * @param string $lifeTime
	 * @return bool
	 */
	public function save(string $id, $data, string $lifeTime = Adapter::LIFE_TIME_1_WEEK): bool
	{
		return $this->adapter->setLifeTime($lifeTime)->save($id, $data);
	}
	
	/**
	 * @param string $id
	 * @return bool
	 */
	public function delete(string $id): bool
	{
		return $this->adapter->delete($id);
	}
	
	/**
	 * @return void
	 */
	public function clearAll(): void
	{
		$this->adapter->clearAll();
	}
	
}