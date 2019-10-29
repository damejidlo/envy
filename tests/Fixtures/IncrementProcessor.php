<?php
declare(strict_types = 1);

namespace DamejidloTests\Envy\Fixtures;

use Damejidlo\Envy\ProcessorInterface;
use Damejidlo\Envy\ValueProviderInterface;
use Nette\SmartObject;
use Nette\Utils\Validators;



final class IncrementProcessor implements ProcessorInterface
{

	use SmartObject;



	/**
	 * @param string $name
	 * @param ValueProviderInterface $valueProvider
	 * @return mixed
	 */
	public function process(string $name, ValueProviderInterface $valueProvider)
	{
		$value = $valueProvider->get($name);

		Validators::assert($value, 'int');
		$value++;

		return $value;
	}

}
