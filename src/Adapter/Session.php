<?php


namespace Vitrex\Cache\Adapter;

/**
 * Class Session
 *
 * @package Vitrex\Cache\Adapter
 */
class Session extends Adapter
{
	private const SESSION_NAME = '_VITREX_CACHE_';
	
	/**
	 * Session constructor.
	 */
	public function __construct()
	{
		if (session_id() === '') {
			session_start();
		}
		
		if (!isset($_SESSION[ self::SESSION_NAME ])) {
			$_SESSION[ self::SESSION_NAME ] = [];
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function load(string $id)
	{
		$cleanId = $id;
		
		$id = $this->hashId($id);
		if (!isset($_SESSION[ self::SESSION_NAME ][ $id ])) {
			return false;
		}
		
		$data    = null;
		$session = unserialize($_SESSION[ self::SESSION_NAME ][ $id ], ['allowed_classes' => true]);
		if (($session[ 'lifeTime' ] === 0) || ((time() - $session[ 'start' ]) <= $session[ 'lifeTime' ])) {
			$data = $session[ 'data' ];
		} else {
			$this->delete($cleanId);
			
			return false;
		}
		
		return $data;
	}
	
	/**
	 * @param string $id
	 * @param $data
	 * @return bool
	 * @throws \Exception
	 */
	public function save(string $id, $data): bool
	{
		$_SESSION[ self::SESSION_NAME ][ $this->hashId($id) ] = serialize([
			'start'    => time(),
			'lifeTime' => $this->getLifeTime(true),
			'data'     => $data,
		]);
		
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	public function delete(string $id): bool
	{
		$id = $this->hashId($id);
		if (isset($_SESSION[ self::SESSION_NAME ][ $id ])) {
			unset($_SESSION[ self::SESSION_NAME ][ $id ]);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * @inheritDoc
	 */
	public function clearAll(): void
	{
		$_SESSION[ self::SESSION_NAME ] = [];
	}
}