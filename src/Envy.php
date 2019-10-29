<?php
declare(strict_types = 1);

namespace Damejidlo\Envy;

use Damejidlo\Envy\ValueProviders\Reader;
use Nette\SmartObject;



final class Envy
{

	use SmartObject;

	/**
	 * @var Reader
	 */
	private $reader;

	/**
	 * @var LoaderFactory
	 */
	private $loaderFactory;



	public function __construct(Reader $reader, LoaderFactory $loaderFactory)
	{
		$this->reader = $reader;
		$this->loaderFactory = $loaderFactory;
	}



	public function exists(string $name) : bool
	{
		return $this->reader->exists($name);
	}



	public function getString(string $name, ?string $default = NULL) : string
	{
		$loader = $this->loaderFactory->create();
		if ($default !== NULL) {
			$loader = $loader->withFallback($default);
		}
		return $loader->get($name);
	}



	public function getStringOrNull(string $name) : ?string
	{
		return $this->loaderFactory->create()->withFallback(NULL)->get($name);
	}



	public function getBool(string $name, ?bool $default = NULL) : bool
	{
		$loader = $this->loaderFactory->createBoolLoader();
		if ($default !== NULL) {
			$loader = $loader->withFallback($default);
		}
		return $loader->get($name);
	}



	public function getBoolOrNull(string $name) : ?bool
	{
		return $this->loaderFactory->createBoolLoader()->withFallback(NULL)->get($name);
	}



	public function getInt(string $name, ?int $default = NULL) : int
	{
		$loader = $this->loaderFactory->createIntLoader();
		if ($default !== NULL) {
			$loader = $loader->withFallback($default);
		}
		return $loader->get($name);
	}



	public function getIntOrNull(string $name) : ?int
	{
		return $this->loaderFactory->createIntLoader()->withFallback(NULL)->get($name);
	}



	public function getFloat(string $name, ?float $default = NULL) : float
	{
		$loader = $this->loaderFactory->createFloatLoader();
		if ($default !== NULL) {
			$loader = $loader->withFallback($default);
		}
		return $loader->get($name);
	}



	public function getFloatOrNull(string $name) : ?float
	{
		return $this->loaderFactory->createFloatLoader()->withFallback(NULL)->get($name);
	}



	/**
	 * @param string $name
	 * @param string[]|NULL $default
	 * @return string[]
	 */
	public function getStringArray(string $name, ?array $default = NULL) : array
	{
		$loader = $this->loaderFactory->createArrayLoader();
		if ($default !== NULL) {
			$loader = $loader->withFallback($default);
		}
		return $loader->get($name);
	}



	/**
	 * @param string $name
	 * @return string[]|NULL
	 */
	public function getStringArrayOrNull(string $name) : ?array
	{
		return $this->loaderFactory->createArrayLoader()->withFallback(NULL)->get($name);
	}



	/**
	 * @param string $name
	 * @param int[]|NULL $default
	 * @return int[]
	 */
	public function getIntArray(string $name, ?array $default = NULL) : array
	{
		$loader = $this->loaderFactory->createIntArrayLoader();
		if ($default !== NULL) {
			$loader = $loader->withFallback($default);
		}
		return $loader->get($name);
	}



	/**
	 * @param string $name
	 * @return int[]|NULL
	 */
	public function getIntArrayOrNull(string $name) : ?array
	{
		return $this->loaderFactory->createIntArrayLoader()->withFallback(NULL)->get($name);
	}



	/**
	 * @param string $name
	 * @param float[]|NULL $default
	 * @return float[]
	 */
	public function getFloatArray(string $name, ?array $default = NULL) : array
	{
		$loader = $this->loaderFactory->createFloatArrayLoader();
		if ($default !== NULL) {
			$loader = $loader->withFallback($default);
		}
		return $loader->get($name);
	}



	/**
	 * @param string $name
	 * @return float[]|NULL
	 */
	public function getFloatArrayOrNull(string $name) : ?array
	{
		return $this->loaderFactory->createFloatArrayLoader()->withFallback(NULL)->get($name);
	}

}
