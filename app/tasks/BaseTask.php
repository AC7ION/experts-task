<?php

/**
 * Base Task with conditions initialization
 * 
 * @author Andrew Korniychuk
 * @version 1.0
 */

namespace Tasks;

use \Cli\Output as Output;
use Phalcon\Cli\Task;


define('BASE_DIR', dirname(__DIR__));
define('INPUT_DIR', dirname(__DIR__) . '/input/');


class BaseTask extends Task {

	protected $directionsNum = 0;
	protected $expertsNum = 0;

	protected $costs = array();
	protected $couldSolve = array();


	public function initialize($params = array())
	{

		if (!empty($params)) {
			$this->readInputFromFile($params);
		}

	}


	public function mainAction()
	{
		Output::stdout("Main Action ");
	}


	protected function readInputFromFile($params)
	{

		$i = 0;
		$file = fopen(INPUT_DIR . 'input' . $params[0] . '.txt', 'r');

		while (!feof($file)) {
			$line = fgets($file);
			$members = explode(' ', $line);

			// directions and experts numbers initialisation
			if ($i == 0) {
				$this->directionsNum = (int)$members[0];
				$this->expertsNum = (int)$members[1];
			}

			// what direction experts could solve
			elseif ($i <= $this->expertsNum) {
				$this->setSolvesMatrix($i, $members);
			}

			// experts costs
			elseif ($i > $this->expertsNum) {
				$this->setCostsArray($members);
			}

			$i++;
		}

		fclose($file);

	}


	protected function setSolvesMatrix($i, array $members)
	{
		if (!isset($this->couldSolve[$i]) || !is_array($this->couldSolve[$i])) {
			$this->couldSolve[$i] = array();
		}

		foreach ($members as $member) {
			$this->couldSolve[$i][] = (int)$member;
		}
	}


	protected function setCostsArray($members)
	{
		foreach ($members as $member) {
			$this->costs[] = (int)$member;
		}
	}

}