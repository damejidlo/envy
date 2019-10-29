<?php
declare(strict_types = 1);

namespace Damejidlo\Envy\Processors\TypeCasting;

use Damejidlo\Envy\ProcessorInterface;
use Damejidlo\Envy\ValueProviderInterface;
use Nette\SmartObject;
use Nette\Utils\AssertionException;
use Nette\Utils\Strings;
use Nette\Utils\Validators;



final class ToBoolProcessor implements ProcessorInterface
{

	use SmartObject;

	private const BOOLEAN_REPRESENTATION = [
		'true' => TRUE,
		'false' => FALSE,
		'1' => TRUE,
		'0' => FALSE,
		'yes' => TRUE,
		'no' => FALSE,
	];



	public function process(string $name, ValueProviderInterface $valueProvider) : bool
	{
		$value = $valueProvider->get($name);

		if (is_bool($value)) {
			return $value;
		}

		Validators::assert($value, 'string', $name);

		$lowerCaseValue = Strings::lower($value);
		if (array_key_exists($lowerCaseValue, self::BOOLEAN_REPRESENTATION)) {
			return self::BOOLEAN_REPRESENTATION[$lowerCaseValue];
		}

		$booleanValues = "'" . implode("', '", array_keys(self::BOOLEAN_REPRESENTATION)) . "'";
		throw new AssertionException("The $name expects to be one of $booleanValues, but got '$value'.");
	}

}
