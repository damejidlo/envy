<?php
declare(strict_types = 1);

namespace Damejidlo\Envy\Processors\TypeCasting;

use Damejidlo\Envy\ProcessorInterface;
use Damejidlo\Envy\ValueProviderInterface;
use Nette\SmartObject;
use Nette\Utils\Validators;



final class ToFloatProcessor implements ProcessorInterface
{

	use SmartObject;



	public function process(string $name, ValueProviderInterface $valueProvider) : float
	{
		$value = $valueProvider->get($name);
		Validators::assert($value, 'numeric', $name);
		return (float) $value;
	}

}
