<?php

namespace Cli\Test;

use Tasks\BaseTask;

/**
 * Test class for Cli\Execute class
 */
class InputTest extends \PHPUnit_Framework_TestCase
{
	protected $_cmd;
	protected $_input;
	protected $_test;


	public function setUp(){
        $this->_cmd = \Cli\Execute::singleton();
		$line[] = '<?php';
		$line[] = 'echo "test" ."123";';
		$this->_input = implode(PHP_EOL, $line);

		$this->_test = new BaseTask();
	}


	public function testFileRead()
	{
		$this->_test->readInputFromFile(array(1));

		// check correct directions num
		$this->assertTrue($this->_test->directionsNum == count($this->_test->couldSolve));

		// check correct experts num
		$this->assertTrue($this->_test->expertsNum == count($this->_test->costs));
	}


	public function testFullExperts()
	{
		$this->_test->readInputFromFile(array(1));

		$experts = array();
		for ($i = 0; $i < $this->_test->expertsNum; $i++) {
			$experts[] = $i;
		}

		$this->assertTrue($this->_test->isExpertsSolvesTheTask($experts),
			'Кожен з напрямів повинен вирішувати хоча б один експерт');
	}



}