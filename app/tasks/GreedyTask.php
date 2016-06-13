<?php

/**
 * Greedy algorithm implementation of experts task
 * 
 * @author Andrew Korniychuk
 * @version 1.0
 */

namespace Tasks;

use \Cli\Output as Output;


class GreedyTask extends BaseTask
{

	/**
	 * Entry point of Greedy algorithm
	 *
	 * @param $params
	 */
	public function mainAction($params)
	{
		// initialize parent task
		// with reading from files etc.
		parent::initialize($params);

		Output::stdOutGreen("Greedy algorithm");

		$selectedExperts = array();
		foreach ($this->couldSolve as $direction) {
			$selectedExpert = $this->findBestExpertOnCurrentStep($direction, $selectedExperts);
			if ($selectedExpert != -1) {
				$selectedExperts[] = $selectedExpert;
			}
			print_r($selectedExperts);

			if ($this->isExpertsSolvesTheTask($selectedExperts)) {
				Output::stdOutGreen("Task SOLVED!");
				Output::stdout("Total cost: " . $this->countTotalSum($selectedExperts));
				break;
			}
		}

//		echo $this->countTotalSum(array(0, 3, 5)) . "\n";
//		echo $this->isExpertAllowed(5, 2) . "\n";
//		print_r($this->getExpertsDirections(0));
//		$this->isExpertsSolvesTheTask(array(0,1,2,3,4,5));
	}


	/**
	 * Finds the best expert ONLY on current step
	 * (greedy algorithm)
	 *
	 * @param $experts
	 * @param $deniedExperts
	 * @return int|string
	 */
	protected function findBestExpertOnCurrentStep($experts, $deniedExperts)
	{
		$currentCost = 0;
		$bestExpert = -1;
		foreach ($experts as $key => $expert) {
			if ($expert && !in_array($key, $deniedExperts)) {
				$currentCost = $this->costs[$key];
				$bestExpert = $key;
				break;
			}
		}

		foreach ($experts as $key => $expert) {
			if ($expert && !in_array($key, $deniedExperts)) {
				if ($currentCost > $this->costs[$key]) {
					$currentCost = $this->costs[$key];
					$bestExpert = $key;
				}
			}

		}

		Output::stdout("Best expert on current step: " . $bestExpert);
		return $bestExpert;
	}

}
