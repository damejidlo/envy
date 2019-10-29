<?php
declare(strict_types = 1);

namespace Damejidlo\Envy\ValueProviders;

use Damejidlo\Envy\ValueProviderInterface;
use Nette\SmartObject;



final class SingleValueProvider implements ValueProviderInterface
{

	use SmartObject;

	/**
	 * @var mixed
	 */
	private $value;



	/**
	 * @param mixed $value
	 */
	public function __construct($value)
	{
		$this->value = $value;
	}



	/**
	 * @param string $name
	 * @return mixed
	 */
	public function get(string $name)
	{
		return $this->value;
	}

}
