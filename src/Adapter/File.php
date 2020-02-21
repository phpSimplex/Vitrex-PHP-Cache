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
	public function __construct(string $fileLocation)
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
			if (!file_exists($this->getFileLocation()) && !mkdir($folder = $this->getFileLocation()) && !is_dir($folder)) {
				throw new AdapterException(sprintf('Directory "%s" was not created', $folder));
			}
		} catch (AdapterException $exception) {
			return $exception->getMessage();
		}
		
		$cacheFile = $this->getFileLocation() . $this->hashId($id);
		if (file_put_contents($cacheFile, serialize($data)) !== false) {
			touch($cacheFile, $this->getLifeTime(true)); // Edit file touch
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * @param string $id
	 * @return bool
	 * @throws AdapterException
	 */
	public function delete(string $id): bool
	{
		// Make sure we're having a hashed file id
		if ($this->isHashed($id)) {
			$id = $this->hashId($id);
		}
		
		$cacheFile = $this->getFileLocation() . $id;
		if (!file_exists($cacheFile)) {
			throw new AdapterException(sprintf('Cache file "%s" not found in "%s"', $id, $this->getFileLocation()));
		}
		
		return @unlink($cacheFile);
	}
	
	/**
	 * @return void
	 * @throws AdapterException
	 * @throws \Exception
	 */
	public function clearAll(): void
	{
		$directoryIterator = new \DirectoryIterator($this->getFileLocation());
		foreach ($directoryIterator as $file) {
			// Make sure were only deleting sha1 hashed files
			if ($file->isDot() && !$this->isHashed($file->getFilename())) {
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
	public function setFileLocation(string $location): File
	{
		$this->fileLocation = $location;
		
		return $this;
	}
	
	/**
	 * @return string
	 * @throws AdapterException
	 */
	public function getFileLocation(): string
	{
		if (null === $this->fileLocation) {
			throw new AdapterException('Initialize a file location first..');
		}
		
		return rtrim($this->fileLocation, '/') . '/';
	}
}