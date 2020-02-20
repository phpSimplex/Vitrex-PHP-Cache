<?php


namespace Vitrex\Cache\Adapter;


use Vitrex\Cache\Exceptions\AdapterException;

/**
 * Class File
 *
 * @package Vitrex\Cache\Adapter
 */
class File extends Adapter
{
	
	/**
	 * @var string Set base file location
	 */
	protected string $fileLocation;
	
	/**
	 * File constructor.
	 *
	 * @param string|null $fileLocation
	 * @throws \Exception
	 */
	public function __construct(?string $fileLocation = null)
	{
		$this->setFileLocation($fileLocation);
	}
	
	/**
	 * @param string $id
	 * @return bool|mixed
	 * @throws AdapterException
	 */
	public function load(string $id)
	{
		$file = $this->getFileLocation() . $this->hashId($id);
		if (!file_exists($file)) {
			return false;
		}
		
		if (filemtime($file) < time()) {
			$this->delete($file);
			
			return false;
		}
		
		return unserialize(file_get_contents($file), ['allowed_classes' => true]);
	}
	
	/**
	 * @param string $id
	 * @param $data
	 * @return bool
	 * @throws \Exception
	 */
	public function save(string $id, $data): bool
	{
		try {
			// Check if folder exist and create
			if (null === $this->fileLocation && !file_exists($this->getFileLocation()) && !mkdir($folder = $this->getFileLocation()) && !is_dir($folder)) {
				throw new AdapterException(sprintf('Directory "%s" was not created', $folder));
			}
		} catch (AdapterException $exception) {
			return $exception->getMessage();
		}
		
		$cacheFile = $this->getFileLocation() . $this->hashId($id);
		$saved     = file_put_contents($cacheFile, serialize($data));
		touch($cacheFile, $this->getLifeTime(true));
		
		return $saved !== false;
	}
	
	/**
	 * @param string $id
	 * @return bool
	 * @throws AdapterException
	 */
	public function delete(string $id): bool
	{
		$cacheFile = $this->getFileLocation() . $this->hashId($id);
		if (!file_exists($cacheFile)) {
			throw new AdapterException(sprintf('Cache file "%s" not found in "%s"', $id, $this->getFileLocation()));
		}
		
		return @unlink($cacheFile);
	}
	
	/**
	 * @param bool $onlyExpiredFiles
	 * @return void
	 * @throws AdapterException
	 * @throws \Exception
	 */
	public function clearAll(bool $onlyExpiredFiles = false): void
	{
		$directoryIterator = new \DirectoryIterator($this->getFileLocation());
		foreach ($directoryIterator as $file) {
			// Make sure we're only deleting filename with sha1 encoding
			if ($file->isDot() || !preg_match('/^[0-9a-f]{40}$/i', $file->getFilename())) {
				continue;
			}
			
			$this->delete($file->getFilename());
		}
	}
	
	/**
	 * Set base file location
	 *
	 * @param string $location
	 * @return File
	 */
	public function setFileLocation(?string $location): File
	{
		if (null !== $location) {
			$this->fileLocation = realpath($location);
		}
		
		return $this;
	}
	
	/**
	 * @return string
	 * @throws AdapterException
	 */
	private function getFileLocation(): string
	{
		if (null === $this->fileLocation) {
			throw new AdapterException('Initialize a file location first..');
		}
		
		return rtrim($this->fileLocation, '/') . '/';
	}
}