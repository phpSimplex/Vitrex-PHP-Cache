<?php


namespace Vitrex\Cache\Adapter;


/**
 * Class AbstractAdapter
 *
 * @package Adapter
 */
abstract class Adapter
{
	
	/** @var string Global Life times */
	public const LIFE_TIME_1_MINUTE = '1 MINUTE';
	public const LIFE_TIME_1_HOUR   = '1 HOUR';
	public const LIFE_TIME_1_WEEK   = '1 WEEK';
	public const LIFE_TIME_1_MONTH  = '1 MONTH';
	public const LIFE_TIME_1_YEAR   = '1 YEAR';
	
	/**
	 * @var string Cache life time
	 */
	protected string $lifeTime = self::LIFE_TIME_1_WEEK;
	
	
	abstract public function load(string $id);
	
	abstract public function save(string $id, $data): bool;
	
	abstract public function delete(string $id): bool;
	
	abstract public function clearAll(): void;
	
	
	/**
	 * @param string $lifeTime
	 * @return $this
	 */
	public function setLifeTime(string $lifeTime = '1 WEEK'): self
	{
		$this->lifeTime = strtoupper(str_ireplace(['-', '+'], '', $lifeTime));
		
		return $this;
	}
	
	/**
	 * Get the lifetime of the cache.
	 *
	 * @param bool $timeStamp
	 * @return string|int
	 * @throws \Exception
	 */
	public function getLifeTime(bool $timeStamp = false)
	{
		$DateTime = new \DateTime('+' . $this->lifeTime);
		if ($timeStamp) {
			return $DateTime->getTimestamp();
		}
		
		return $DateTime->format('d M Y H:i:s');
	}
	
	/**
	 * Hash the cache id.
	 *
	 * @param string $id
	 * @return string
	 */
	protected function hashId(string $id): string
	{
		return sha1(strtolower(str_ireplace('\\', '_', $id)), false);
	}
	
}