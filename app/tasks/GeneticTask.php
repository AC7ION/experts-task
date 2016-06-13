<?php

/**
 * Greedy algorithm implementation of experts task
 * 
 * @author Andrew Korniychuk
 * @version 1.0
 */

namespace Tasks;

use \Cli\Output as Output;


class GeneticTask extends BaseTask
{


	public function mainAction($params)
	{
		parent::initialize($params);

		Output::stdOutGreen(" Greedy algorithm");



	}

}
