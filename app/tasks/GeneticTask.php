<?php

/**
 * Greedy algorithm implementation of experts task
 *
 * @author Andrew Korniychuk
 * @version 1.0
 */

namespace Tasks;

use \Cli\Output as Output;
const QUESTIONS = 50;

class GeneticTask extends BaseTask
{
    private $population = array();

    public function mainAction($params)
    {
        parent::initialize($params);

        Output::stdOutGreen("Genetic algorithm");
        $individuals = $this->analysisIndividuals();
        $bests = $this->bestIndividuals($individuals ['prices'], $individuals['not_assessed']);
//        $this->output_population();
        $this->createСhild($bests);

    }
    /**
     * analysisIndividuals
     * Аналізує всі особини і вибирає хто з них відповів на всі питання, а хто ні
     *
     * @return  array (int) - закінчений масив нових дочірніх особин (створення розбиттям найкраших особин по точкам схрещування)
     * Вивід массиву в лінію
     * */
    private function analysisIndividuals()
    {
        $a = 0;
        $b = 0;
        $i = 0;
        $individual_prices = array();
        $assessed_individuals = array();
        $not_assessed_individuals = array();
        $this->populationRandomise(QUESTIONS);
        $this->output_population();

        foreach ($this->population as $individuals) {
            $experts = $this->respondToTheQuestion($individuals);

            if ($this->isExpertsSolvesTheTask($experts)) {
                $a++;
                $assessed_individuals[] = $i;
            } else {
                $b++;
                $not_assessed_individuals [] = $i;
                unset($this->population[$i]);
            }
            $individual_price = $this->individualPrice($experts);
            $individual_prices[] = $individual_price;
            $i++;
        }
        echo "Ціни особин \n";
//        print_r($individual_prices);

        echo "Задачі виконали $a популяції\n";
        echo "Задачі не виконали $b популяції\n";
        echo "assessed_individuals\n";
        foreach ($assessed_individuals as $expert) {
            echo $expert . " ";
        }
        echo "\n";
        echo "not_assessed_individuals \n";
        foreach ($not_assessed_individuals as $expert) {
            echo $expert . " ";
        }
        echo "\n";

        return array(
            'assessed' => $assessed_individuals,
            'not_assessed' => $not_assessed_individuals,
            'prices' => $individual_prices
        );
    }

    /**
     * createСhild
     * Створення нових особин на заміну тим що вимерли
     *
     * @param array (int)   - індекси найкращих особин серед тих хто дав заключення по всіх питаннях
     * @return  array (int) - закінчений масив нових дочірніх особин (створення розбиттям найкраших особин по точкам схрещування)
     * Вивід массиву в лінію
     * */
    private function createСhild(array $bests)
    {
        $children = array();
        for ($i = 0; $i < count($bests); $i++) {
            $children[] = array();
        }
        $bests_count = count($bests);
        $crossing_points = $this->CrossingPoints($bests);

        for ($i = 0; $i < $bests_count; $i++) {
            echo "Поточний найкращий \n";
            $this->outArr($this->population[$bests[$i]]);
            echo "\n";
            $spliced_array = $this->individualSplice($crossing_points, $bests, $i);
//            echo "spliced_array :\n";
//            print_r($spliced_array);

            $children = $this->individualMerge($i, $bests_count, $children, $spliced_array);


        }

        echo "Нові потомки\n";
        print_r($children);


    }

    /**
     * Вивід масиву в лінію
     * */
    private function output_population()
    {
        echo "Початкова популяція:\n";
        for ($i = 0; $i < count($this->population); $i++) {
            if ($i < 9) {
                echo /*"Питання номер " .*/
                    ($i + 1) . "  |";
            } else {
                echo /*"Питання номер " .*/
                    ($i + 1) . " |";
            }
            for ($j = 0; $j < count($this->population[$i]); $j++) {
                echo $this->population[$i][$j] . "| ";
            }
            echo "\n";
        }
    }

    /**
     * respondToTheQuestion
     * Визначае експертів що є в особині і здатні відповідати на запитання
     *
     * @param array (int)    - вся особина
     * @return  array (int) - індекси експертів що дали заключення
     * */
    private function respondToTheQuestion($individual)
    {
        $experts = array();
        foreach ($individual as $key => $gen) {
            if ($gen == 1) {
                $experts[] = $key;
            }
        }
        return $experts;
    }

    /**
     * individualPrice
     * Ціна однієї особини
     *
     * @param array (int)    - індекси експертів що дали заключення
     * @return  int         - ціна особини
     * */

    private function individualPrice($experts)
    {
        $costs_good_expert = array();
        foreach ($experts as $expert) {
            $costs_good_expert[$expert] = $this->costs[$expert];
        }
        $individual_price = array_sum($costs_good_expert);
//        echo $individual_price."\n";
        return $individual_price;
    }

    /**
     * minKey
     * Ключ найдешевшої особини
     *
     * @param array (int)    - ціни всіх особин що дали заключення по всіх питтаннях
     * @return  array (int) - індекс особини з найменшою ціною
     * */
    private function minKey($individual_prices)
    {
        $min_key = array_keys($individual_prices, min($individual_prices));
        $min_key = $min_key [0];
        return $min_key;
    }

    /**
     * bestIndividuals
     * Ключі найдешевших особин
     *
     * @param array (int)    - ціни всіх особин що дали заключення по всіх питтаннях
     * @param array (int)    - особини що не дали заключення по всім питанням
     * @return  array (int)  - індекси найкращих особин серед тих хто дав заключення по всіх питаннях
     * */
    private function bestIndividuals($individual_prices, $not_assessed_individuals)                 //!!!!!!!!!!!!!!!!!!!
    {
        $need_best = count($not_assessed_individuals);
        $bests = array();
        $i = 0;
        while ($i != $need_best) {
            $min_key = $this->minKey($individual_prices);
            if (!in_array($min_key, $not_assessed_individuals) && !in_array($min_key, $bests) ) {
                $bests[] = $min_key;
                unset($individual_prices[$min_key]);
                $individual_prices = array_values($individual_prices);
                $i++;
            } else {
                unset($individual_prices[$min_key]);

                $individual_prices = array_values($individual_prices);
            }
        }

        echo "Найдешевше коштують особини:\n";

        print_r($bests);

        return $bests;
    }

    /**
     * populationRandomise
     * Ствоенння випадкової популяції
     *
     * @param int   - розмір популяції (кількість питаннь)
     * */
    private function populationRandomise($populationSize)
    {
        for ($i = 0; $i < $populationSize; $i++) {
            $single = array();
            for ($j = 0; $j < $this->expertsNum; $j++) {
                $single[] = mt_rand(0, 1);
            }
            $this->population [] = $single;
        }
    }

    /**
     * outArr
     * Вивід одновимірного масиву в лінію
     *
     * @param array (int) - масив для виводу
     * */
    private function outArr(array $arr)
    {
        for ($i = 0; $i < count($arr); $i++) {
            echo $arr[$i] . " ";
        }
        echo "\n";
    }

    /**
     * CrossingPoints
     * Точки схрещування
     *
     * @param   array (int) - індекси найкращих особин серед тих хто дав заключення по всіх питаннях
     * @return  array (int) - точки схрещування
     * */

    private function CrossingPoints($bests)
    {
        $crossing_points = array();
        $quantity = count($bests);

        while (count($crossing_points) != $quantity - 1) {
            $crossing_point = mt_rand(1, $this->expertsNum - 1);
            if (!in_array($crossing_point, $crossing_points)) {
                $crossing_points[] = $crossing_point;
            }
        }
        $crossing_points[] = 0;
        echo "Точки схрещування:\n";
        rsort($crossing_points);
        $this->outArr($crossing_points);

        return $crossing_points;
    }

    /**
     * individualSplice
     * Ділить масив відносно точок схрещування
     *
     * @param array (int)   - точки схрещування
     * @param array (int)   - індекси найкращих особин серед тих хто дав заключення по всіх питаннях
     * @param int           - індекс зовнішнього масиву
     * @return  array (int) - масив поділений відносно точок схрещування
     * */
    private function individualSplice($crossing_points, $bests, $i)
    {
        $spliced_array = array();
        for ($j = 0; $j < count($crossing_points); $j++) {
            $this->population[$bests[$i]];
            $spliced_array[] = array_splice($this->population[$bests[$i]], $crossing_points[$j]);
        }
        return $spliced_array;
    }

    /**
     * individualMerge
     * Проводить ітерацію по злиттю із individualSplice() кожної з найкращих особин
     *
     * @param int           - індекс зовнішнього масиву
     * @param int           - кількість найкращих особин (визначається на основі того скілки особин вимерли)
     * @param array (int)   - масив нових дочірніх особин на момент злиття(створення розбиттям найкраших особин по точкам схрещування)
     * @param array (int)   - масив вже розбитої відносно точок схрещування особини
     * @return  array (int) - масив нових дочірніх особин на момент злиття(створення розбиттям найкраших особин по точкам схрещування)
     * */
    private function individualMerge($i, $bests_count, $children, $spliced_array)
    {
        $k = 0;
        for ($j = $i; $j < $bests_count; $j++) {
            $children[$j] = array_merge($children[$j], $spliced_array[$k]);
            $k++;
        }
        for ($n = 0; $n < $i; $n++) {
            $children[$n] = array_merge($children[$n], $spliced_array[$k]);
            $k++;

        }
        return $children;
    }




}
