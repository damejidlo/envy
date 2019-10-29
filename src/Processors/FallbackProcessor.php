<?php
declare(strict_types = 1);

namespace Damejidlo\Envy\Processors;

use Damejidlo\Envy\EnvironmentVariableNotFoundException;
use Damejidlo\Envy\ProcessorInterface;
use Damejidlo\Envy\ValueProviderInterface;
use Nette\SmartObject;



final class FallbackProcessor implements ProcessorInterface
{

	use SmartObject;

	/**
	 * @var mixed
	 */
	private $fallback;



	/**
	 * @param mixed $fallback
	 */
	public function __construct($fallback)
	{
		$this->fallback = $fallback;
	}



	/**
	 * @param string $name
	 * @param ValueProviderInterface $valueProvider
	 * @return mixed
	 */
	public function process(string $name, ValueProviderInterface $valueProvider)
	{
		try {
			return $valueProvider->get($name);
		} catch (EnvironmentVariableNotFoundException $exception) {
			return $this->fallback;
		}
	}

}
