<?php

/**
 * Greedy algorithm implementation of experts task
 *
 * @author  Andrew Korniychuk
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

        $selectedExperts = array();
        $selectedBestExperts = array();
        $selectedDirections = array();
        //		$bestSolution = 0;

        $i = 0;

        foreach ($this->couldSolve as $dirNum => $direction) {

            foreach ($direction as $key => $expert) {
                if ($expert) {
                    if ($i == 0) {
                        $selectedExperts[] = array($key);
                    } else {
                        for ($j = 0; $j < count($selectedExperts); $j++) {
                            array_push($selectedExperts[$j], $key);
                            $selectedExperts[$j] = array_unique($selectedExperts[$j]);
//                            if ($this->isExpertsSolvesTheTask($selectedExperts[$j])) {
//                                print_r($selectedExperts[$j]);
//                                $totalSum = $this->countTotalSum($selectedExperts[$j]);
//                                if (!isset($bestSolution)) {
//                                    $bestSolution = $totalSum;
//                                } else {
//                                    if ($bestSolution > $totalSum) {
//                                        $bestSolution = $totalSum;
//                                    }
//                                }
//                                $temp = 1;
//                                break;
//                            }


                        }
                        $selectedExperts[] = array($key);

                        for ($j = 0; $j < count($selectedExperts); $j++) {
                            if ($this->isExpertsSolvesTheTask($selectedExperts[$j])) {
//                                print_r($selectedExperts[$j]);
                                $totalSum = $this->countTotalSum($selectedExperts[$j]);
                                if (!isset($bestSolution)) {
                                    $bestSolution = $totalSum;
                                } else {
                                    if ($bestSolution > $totalSum) {
                                        $selectedBestExperts = $selectedExperts[$j];
                                        $bestSolution = $totalSum;
                                    }
                                }
                                $temp = 1;
                                break;
                            }
                        }

                    }
                }
            }
            $i++;

        }

        Output::stdOutGreen("Task SOLVED!");
        Output::stdout("Total cost: " . $bestSolution);
        print_r($selectedBestExperts);


        //		print_r($selectedExperts);


    }


}
