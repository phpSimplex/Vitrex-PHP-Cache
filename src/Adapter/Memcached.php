<?php


namespace Vitrex\Cache\Adapter;

use Vitrex\Cache\Exceptions\AdapterException;

/**
 * Class Memcached
 *
 * @package Vitrex\Cache\Adapter
 */
class Memcached extends Adapter
{
	/**
	 * @var \Memcached
	 */
	protected \Memcached $memcached;
	
	/** Memcached version */
	protected string $version;
	
	/**
	 * Memcached constructor.
	 *
	 * @param string $host
	 * @param int $port
	 * @param int $weight
	 * @throws AdapterException
	 */
	public function __construct(string $host = 'localhost', int $port = 11211, int $weight = 1)
	{
		if (!class_exists('Memcached', false)) {
			throw new AdapterException('Error: Memcached is not available.');
		}
		
		$this->memcached = new \Memcached();
		$this->addServer($host, $port, $weight);
		
		$version = $this->memcached->getVersion();
		$ver     = $host . ':' . $port;
		if (isset($version[ $ver ])) {
			$this->version = $version[ $ver ];
		}
	}
	
	/**
	 * @param $host
	 * @param int $port
	 * @param int $weight
	 * @return Memcached
	 */
	public function addServer(string $host, int $port = 11211, int $weight = 1): Memcached
	{
		$this->memcached->addServer($host, $port, $weight);
		
		return $this;
	}
	
	/**
	 * @inheritDoc
	 */
	public function load(string $id)
	{
		$memCached = $this->memcached->get($id);
		
		$value = null;
		if (($memCached !== false) &&
			(($memCached[ 'lifeTime' ] === 0) || ((time() - $memCached[ 'start' ]) <= $memCached[ 'lifeTime' ]))) {
			$value = $memCached[ 'data' ];
		} else {
			$this->delete($id);
			
			return false;
		}
		
		return $value;
	}
	
	/**
	 * @param string $id
	 * @param $data
	 * @return bool
	 * @throws \Exception
	 */
	public function save(string $id, $data): bool
	{
		$memCached = [
			'start'    => time(),
			'lifeTime' => $this->getLifeTime(true),
			'data'     => $data,
		];
		
		return $this->memcached->set($id, $memCached, $memCached[ 'lifeTime' ]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function delete(string $id): bool
	{
		return $this->memcached->delete($id);
	}
	
	/**
	 * @inheritDoc
	 */
	public function clearAll(): void
	{
		$this->memcached->flush();
	}
}