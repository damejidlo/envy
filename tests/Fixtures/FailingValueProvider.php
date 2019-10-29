<?php
declare(strict_types = 1);

namespace DamejidloTests\Envy\Fixtures;

use Damejidlo\Envy\EnvironmentVariableNotFoundException;
use Damejidlo\Envy\ValueProviderInterface;
use Nette\SmartObject;



final class FailingValueProvider implements ValueProviderInterface
{

	use SmartObject;

	/**
	 * @var \Throwable|NULL
	 */
	private $failureException;



	public function __construct(?\Throwable $failureException = NULL)
	{
		$this->failureException = $failureException;
	}



	/**
	 * @param string $name
	 * @return mixed
	 */
	public function get(string $name)
	{
		if ($this->failureException !== NULL) {
			throw $this->failureException;
		}

		throw EnvironmentVariableNotFoundException::withName($name);
	}

}
