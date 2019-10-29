<?php
declare(strict_types = 1);

namespace Damejidlo\Envy\Processors;

use Damejidlo\Envy\ProcessorInterface;
use Damejidlo\Envy\ValueProviderInterface;
use Nette\SmartObject;
use Nette\Utils\Validators;



final class ValidatorProcessor implements ProcessorInterface
{

	use SmartObject;

	/**
	 * @var string
	 */
	private $type;



	public function __construct(string $type)
	{
		$this->type = $type;
	}



	/**
	 * @param string $name
	 * @param ValueProviderInterface $valueProvider
	 * @return mixed
	 */
	public function process(string $name, ValueProviderInterface $valueProvider)
	{
		$value = $valueProvider->get($name);
		Validators::assert($value, $this->type, $name);
		return $value;
	}

}
