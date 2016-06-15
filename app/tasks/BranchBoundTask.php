<?php

/**
 * Greedy algorithm implementation of experts task
 * 
 * @author Andrew Korniychuk
 * @version 1.0
 */

namespace Tasks;

use \Cli\Output as Output;


class BranchBoundTask extends BaseTask
{

	/**
	 * Entry point of Branch&Bound algorithm
	 *
	 * @param $params
	 */
	public function mainAction($params)
	{
		// initialize parent task
		// with reading from files etc.
		parent::initialize($params);

		Output::stdOutGreen("Branch&Bound algorithm");


	}



}
