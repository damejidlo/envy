<?php
declare(strict_types = 1);

namespace DamejidloTests\Envy\Processors\TypeCasting;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\Envy\Processors\TypeCasting\ToBoolProcessor;
use Damejidlo\Envy\ValueProviders\SingleValueProvider;
use DamejidloTests\Envy\TestCase;
use Nette\Utils\AssertionException;
use Tester\Assert;



/**
 * @testCase
 */
class ToBoolProcessorTest extends TestCase
{

	/**
	 * @dataProvider getValidData
	 * @param mixed $input
	 * @param bool $output
	 */
	public function testValidConversion($input, bool $output) : void
	{
		$processor = new ToBoolProcessor();
		Assert::same($output, $processor->process('foo', new SingleValueProvider($input)));
	}



	/**
	 * @return mixed[]
	 */
	protected function getValidData() : array
	{
		return [
			[
				'input' => TRUE,
				'output' => TRUE,
			],
			[
				'input' => FALSE,
				'output' => FALSE,
			],
			[
				'input' => 'true',
				'output' => TRUE,
			],
			[
				'input' => 'false',
				'output' => FALSE,
			],
			[
				'input' => 'yes',
				'output' => TRUE,
			],
			[
				'input' => 'no',
				'output' => FALSE,
			],
			[
				'input' => '1',
				'output' => TRUE,
			],
			[
				'input' => '0',
				'output' => FALSE,
			],
			[
				'input' => 'tRUe',
				'output' => TRUE,
			],
			[
				'input' => 'fALSe',
				'output' => FALSE,
			],
		];
	}



	/**
	 * @dataProvider getInvalidData
	 * @param mixed $input
	 */
	public function testInvalidConversion($input) : void
	{
		$processor = new ToBoolProcessor();
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
			['input' => 42],
			['input' => 3.1415],
			['input' => NULL],
		];
	}

}



(new ToBoolProcessorTest())->run();
