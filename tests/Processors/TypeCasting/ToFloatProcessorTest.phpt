<?php
declare(strict_types = 1);

namespace DamejidloTests\Envy\Processors\TypeCasting;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\Envy\Processors\TypeCasting\ToFloatProcessor;
use Damejidlo\Envy\ValueProviders\SingleValueProvider;
use DamejidloTests\Envy\TestCase;
use Nette\Utils\AssertionException;
use Tester\Assert;



/**
 * @testCase
 */
class ToFloatProcessorTest extends TestCase
{

	/**
	 * @dataProvider getValidData
	 * @param mixed $input
	 * @param float $output
	 */
	public function testValidConversion($input, float $output) : void
	{
		$processor = new ToFloatProcessor();
		Assert::equal($output, $processor->process('foo', new SingleValueProvider($input)));
	}



	/**
	 * @return mixed[]
	 */
	protected function getValidData() : array
	{
		return [
			[
				'input' => 3.1415,
				'output' => 3.1415,
			],
			[
				'input' => 42,
				'output' => 42.0,
			],
			[
				'input' => '-42.3',
				'output' => -42.3,
			],
			[
				'input' => '50',
				'output' => 50.0,
			],
		];
	}



	/**
	 * @dataProvider getInvalidData
	 * @param mixed $input
	 */
	public function testInvalidConversion($input) : void
	{
		$processor = new ToFloatProcessor();
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
	protected function getInvalidData() : array
	{
		return [
			['input' => ''],
			['input' => 'bflmpsvz'],
			['input' => NULL],
		];
	}

}



(new ToFloatProcessorTest())->run();
