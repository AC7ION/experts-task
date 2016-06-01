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

	public function mainAction($params)
	{
		parent::initialize($params);

		Output::stdOutGreen("Greedy algorithm");

//		echo $this->countTotalSum(array(0, 3, 5)) . "\n";
//		echo $this->isExpertAllowed(5, 2) . "\n";
//		print_r($this->getExpertsDirections(0));
		$this->isExpertsSolvesTheTask(array(0,1,2,3,4,5));
	}

}
