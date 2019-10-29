<?php
declare(strict_types = 1);

namespace DamejidloTests\Envy\Processors\TypeCasting;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\Envy\Processors\TypeCasting\ArrayProcessor;
use Damejidlo\Envy\Processors\TypeCasting\ToIntProcessor;
use Damejidlo\Envy\ValueProviders\SingleValueProvider;
use DamejidloTests\Envy\Fixtures\IncrementProcessor;
use DamejidloTests\Envy\TestCase;
use Nette\Utils\AssertionException;
use Tester\Assert;



/**
 * @testCase
 */
class ArrayProcessorTest extends TestCase
{

	public function testEmptyStringIsConvertedToEmptyArray() : void
	{
		$value = new SingleValueProvider('');
		$processor = new ArrayProcessor();
		Assert::same([], $processor->process('foo', $value));
	}



	/**
	 * @dataProvider getInvalidInput
	 * @param mixed $input
	 */
	public function testInvalidInput($input) : void
	{
		$processor = new ArrayProcessor();
		Assert::exception(
			function () use ($processor, $input) : void {
				$processor->process('foo', new SingleValueProvider($input));
			},
			AssertionException::class,
		);
	}



	/**
	 * @return mixed[]
	 */
	protected function getInvalidInput() : array
	{
		return [
			['input' => NULL],
			['input' => 42],
			['input' => []],
		];
	}



	public function testProcessorsOrder() : void
	{
		$processor = new ArrayProcessor(ArrayProcessor::DEFAULT_DELIMITER, new ToIntProcessor());
		$processor = $processor->withItemValidator('int:0..0');
		$processor = $processor->withAddedItemProcessor(new IncrementProcessor());
		$processor = $processor->withItemValidator('int:1..1');
		$processor = $processor->withAddedItemProcessor(new IncrementProcessor());

		Assert::same([2], $processor->process('foo', new SingleValueProvider('0')));
	}



	public function testWithDelimiter() : void
	{
		$value = new SingleValueProvider('0-41:42');
		$processor = new ArrayProcessor('~[-]~');
		$processorWithChangeDelimiter = $processor->withDelimiter('~[:]~');

		Assert::same(['0-41', '42'], $processorWithChangeDelimiter->process('foo', $value));

		// immutability test
		Assert::same(['0', '41:42'], $processor->process('foo', $value));
	}



	public function testWithAddedItemProcessor() : void
	{
		$value = new SingleValueProvider('0, 41');
		$processor = new ArrayProcessor(ArrayProcessor::DEFAULT_DELIMITER, new ToIntProcessor());
		$processorWithIncrement = $processor->withAddedItemProcessor(new IncrementProcessor());

		Assert::same([1, 42], $processorWithIncrement->process('foo', $value));

		// immutability test
		Assert::same([0, 41], $processor->process('foo', $value));
	}



	public function testWithItemValidator() : void
	{
		$value = new SingleValueProvider('https://example.com, not-url');
		$processor = new ArrayProcessor();
		$processorWithValidation = $processor->withItemValidator('url');

		Assert::exception(
			function () use ($processorWithValidation, $value) : void {
				$processorWithValidation->process('foo', $value);
			},
			AssertionException::class,
			"The item of foo expects to be url, string 'not-url' given.",
		);

		// immutability test
		Assert::same(['https://example.com', 'not-url'], $processor->process('foo', $value));
	}

}



(new ArrayProcessorTest())->run();
