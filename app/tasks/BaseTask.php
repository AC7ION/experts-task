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

	// кількість напрямів
	protected $directionsNum = 0;

	// кількість експертів
	protected $expertsNum = 0;

	// матриця вартостей
	protected $costs = array();

	// матриця відповідності експерт -> які задачі може вирішити
	protected $couldSolve = array();


	public function initialize($params = array())
	{

		if (!empty($params)) {
			$this->readInputFromFile($params);
		}

	}


	/**
	 * Entry point
	 */
	public function mainAction()
	{
		Output::stdout("Main Action");
	}


	/**
	 * Reads input data from file
	 *
	 * @param $params
	 */
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
			elseif ($i <= $this->directionsNum) {
				$this->setSolvesMatrix($i, $members);
			}

			// experts costs
			elseif ($i > $this->directionsNum) {
				$this->setCostsArray($members);
			}

			$i++;
		}

		fclose($file);

	}


	/**
	 * Sets solves matrix
	 *
	 * @param       $i
	 * @param array $members
	 */
	protected function setSolvesMatrix($i, array $members)
	{
		if (!isset($this->couldSolve[$i]) || !is_array($this->couldSolve[$i])) {
			$this->couldSolve[$i-1] = array();
		}

		foreach ($members as $member) {
			$this->couldSolve[$i-1][] = (int)$member;
		}
	}


	/**
	 * Sets costs array
	 *
	 * @param $members
	 */
	protected function setCostsArray($members)
	{
		foreach ($members as $member) {
			$this->costs[] = (int)$member;
		}
	}


	/**
	 * Counts total sum for given experts
	 *
	 * @param array $experts
	 * @return int
	 */
	protected function countTotalSum(array $experts)
	{
		$sum = 0;

		foreach ($experts as $expert) {
			if (isset($this->costs[$expert])) {
				$sum += $this->costs[$expert];
			}
		}

		return $sum;
	}


	/**
	 * Is expert could solve given direction
	 *
	 * @param $directionNum
	 * @param $expertNum
	 * @return mixed
	 */
	protected function isExpertAllowed($directionNum, $expertNum)
	{
		return $this->couldSolve[$directionNum][$expertNum];
	}


	/**
	 * Returns directions for given expert
	 *
	 * @param $expertNum
	 * @return array
	 */
	protected function getExpertsDirections($expertNum)
	{
		$allowedDirections = array();

		foreach ($this->couldSolve as $key => $direction) {
			if ($direction[$expertNum]) {
				$allowedDirections[] = $key;
			}
		}

		return $allowedDirections;
	}


	/**
	 * Returns is experts solves the task by experts keys
	 *
	 * @param array $experts array with experts numbers
 	 * @return bool
	 */
	protected function isExpertsSolvesTheTask(array $experts)
	{
		$solvedDirections = array();

		foreach ($experts as $expert) {
			$expertsDirection = $this->getExpertsDirections($expert);
			$solvedDirections = array_merge($solvedDirections, $expertsDirection);
		}

		$solvedDirections = array_unique($solvedDirections);
		//print_r($solvedDirections);

		//echo count($solvedDirections) . "\n";
		return $this->directionsNum == count($solvedDirections);
	}

}