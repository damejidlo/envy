<?php
declare(strict_types = 1);

namespace Damejidlo\Envy;

use Damejidlo\Envy\Processors\TypeCasting\ArrayProcessor;
use Damejidlo\Envy\Processors\TypeCasting\ToBoolProcessor;
use Damejidlo\Envy\Processors\TypeCasting\ToFloatProcessor;
use Damejidlo\Envy\Processors\TypeCasting\ToIntProcessor;
use Damejidlo\Envy\ValueProviders\Loader;
use Damejidlo\Envy\ValueProviders\Reader;
use Nette\SmartObject;



final class LoaderFactory
{

	use SmartObject;

	/**
	 * @var Reader
	 */
	private $reader;



	public function __construct(Reader $reader)
	{
		$this->reader = $reader;
	}



	public function create(ProcessorInterface ...$processors) : Loader
	{
		return new Loader($this->reader, ...$processors);
	}



	public function createBoolLoader() : Loader
	{
		return $this->create(new ToBoolProcessor());
	}



	public function createIntLoader() : Loader
	{
		return $this->create(new ToIntProcessor());
	}



	public function createFloatLoader() : Loader
	{
		return $this->create(new ToFloatProcessor());
	}



	public function createArrayLoader(string $delimiter = ArrayProcessor::DEFAULT_DELIMITER) : Loader
	{
		return $this->create(new ArrayProcessor($delimiter));
	}



	public function createIntArrayLoader(string $delimiter = ArrayProcessor::DEFAULT_DELIMITER) : Loader
	{
		return $this->create(new ArrayProcessor($delimiter, new ToIntProcessor()));
	}



	public function createFloatArrayLoader(string $delimiter = ArrayProcessor::DEFAULT_DELIMITER) : Loader
	{
		return $this->create(new ArrayProcessor($delimiter, new ToFloatProcessor()));
	}

}
