<?php
declare(strict_types = 1);

namespace Damejidlo\Envy\Processors\TypeCasting;

use Damejidlo\Envy\ProcessorInterface;
use Damejidlo\Envy\Processors\MultiProcessor;
use Damejidlo\Envy\Processors\ValidatorProcessor;
use Damejidlo\Envy\ValueProviderInterface;
use Damejidlo\Envy\ValueProviders\SingleValueProvider;
use Nette\SmartObject;
use Nette\Utils\Strings;
use Nette\Utils\Validators;



final class ArrayProcessor implements ProcessorInterface
{

	use SmartObject;

	public const DEFAULT_DELIMITER = '~\s*,\s*~';

	/**
	 * @var string
	 */
	private $delimiter;

	/**
	 * @var MultiProcessor
	 */
	private $itemProcessor;



	/**
	 * @param string $delimiter
	 * @param ProcessorInterface ...$processors
	 * phpcs:disable SlevomatCodingStandard.Functions.UselessParameterDefaultValue.UselessParameterDefaultValue
	 */
	public function __construct(string $delimiter = self::DEFAULT_DELIMITER, ProcessorInterface ...$processors)
	{
		// phpcs:enable
		$this->delimiter = $delimiter;
		$this->itemProcessor = new MultiProcessor(...$processors);
	}



	public function withDelimiter(string $delimiter) : ArrayProcessor
	{
		$clone = clone $this;
		$clone->delimiter = $delimiter;

		return $clone;
	}



	public function withAddedItemProcessor(ProcessorInterface $processor) : ArrayProcessor
	{
		$clone = clone $this;
		$clone->itemProcessor = $clone->itemProcessor->withAddedProcessor($processor);

		return $clone;
	}



	public function withItemValidator(string $type) : ArrayProcessor
	{
		return $this->withAddedItemProcessor(new ValidatorProcessor($type));
	}



	/**
	 * @param string $name
	 * @param ValueProviderInterface $valueProvider
	 * @return mixed[]
	 */
	public function process(string $name, ValueProviderInterface $valueProvider) : array
	{
		$value = $valueProvider->get($name);
		if ($value === '') {
			return [];
		}

		Validators::assert($value, 'string', $name);

		return array_map(
			function (string $itemValue) use ($name) {
				return $this->itemProcessor->process("item of $name", new SingleValueProvider($itemValue));
			},
			Strings::split($value, $this->delimiter),
		);
	}

}
