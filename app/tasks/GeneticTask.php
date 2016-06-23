<?php

/**
 * Greedy algorithm implementation of experts task
 *
 * @author Andrew Korniychuk
 * @version 1.0
 */

namespace Tasks;

use \Cli\Output as Output;
const POPULATION_SIZE = 40;
const ITERATION = 100;

class GeneticTask extends BaseTask
{
    private $population = array();

    public function mainAction($params)
    {
        parent::initialize($params);
        $this->runtime();
        $best_prices = array();
        $best_individuals = array();
        $best_individual = null;
        $this->population = $this->populationRandomise(POPULATION_SIZE);
        $this->output_population();
        Output::stdOutGreen("Genetic algorithm");
        for ($i = 0; $i < ITERATION; $i++) {
            $individuals = $this->analysisIndividuals();
//            $this->output_population();
            if (!empty($individuals['not_assessed'])) {
                $bests = $this->bestIndividuals($individuals ['assessed_prices'], $individuals['not_assessed']);
                $children = $this->createСhild($bests['bests'], $bests['need_random']);
//                $this->output_population();


                $this->insertNewChildrens($individuals['not_assessed'], $children);
                $best_individual = $this->minKey($individuals["assessed_prices"]);
//                $this->output_population();
            }

            $experts = $this->respondToTheQuestion($this->population[$best_individual]);

            $best_price = $this->individualPrice($experts);

            if ($best_price != 0) {
                echo "Поточна найкраща особина " . $best_individual . "\n";
                $best_prices[] = $best_price;
                $best_individuals[] = $best_individual;
                echo "Ціна найкращого " . $best_price . "\n";
//                print_r($this->population[$best_individual]);

            } else {
                echo "Популяція недіездатна вона буде перероблена\n";
            }

        }
        $this->output_population();
        echo "Виконано за: ".$this->runtime()."\n";
//        $best_prices = array_unique($best_prices);
////        print_r($best_prices);
//        $best_key = $this->minKey($best_prices);

//        echo "Знайдений найкраща особина на ітерації " . $best_key . "\n";
//        $experts = $this->respondToTheQuestion($this->population[$best_individuals[$best_key]]);
//        echo min($best_prices )."\n";
//        echo "Ціна найкращого " . $this->individualPrice($experts) . "\n";
//        echo "Найкращий " . $best_individuals[$best_key] . "\n";
//        print_r($this->population[$best_individuals[$best_key]]);


    }


    /**
     * insertNewChildrens
     * Вставляє новосформованих потмків замість особин що вимерли
     *
     * @param   array (int)      - індекси особин що не змргли дати заключення і вимерли
     * @param   array (int)      - массив нових нащадків
     * @return  int             - популяцію із новими нащадками(можна проводити нову ітерацію цикла)
     * */
    private
    function insertNewChildrens(
        array $individuals,
        array $childrens
    ) {
        foreach ($individuals as $individual) {
            $children = array_pop($childrens);
            $this->randomChange($children);
            $this->population[$individual] = $children;
        }
    }


    /**
     * randomChange
     * Вставляє новосформованих потмків замість особин що вимерли
     *
     * @param   array (int)      - новостворена особина
     * @return  int             - популяцію із новими нащадками(можна проводити нову ітерацію цикла)
     * */
    private
    function randomChange(
        array $children
    ) {
        mt_rand(0, $this->expertsNum);
        $experts = $this->respondToTheQuestion($children);
        if (!$this->isExpertsSolvesTheTask($experts)) {

            $this->reanimation($children);

        } else {
            if (mt_rand(0, 1) == 1) {
                $this->selection($children);
            }

            if (mt_rand(0, 1) == 1) {
                $this->mutation($children);

            }

        }
//        $this->output_population();

    }


    /**
     * analysisIndividuals
     * Аналізує всі особини і вибирає хто з них відповів на всі питання, а хто ні і визначає ціну кожної особини
     *
     * @return  array(                                            - масив тих хто дав заключення по всіх питаннях, хто не дав і вартість тих хто дав
     * 'assessed' => $assessed_individuals,
     * 'not_assessed' => $not_assessed_individuals,
     * 'prices' => $individual_prices
     * );
     * */
    private
    function analysisIndividuals()
    {
        $a = 0;
        $b = 0;
        $i = 0;
        $assessed_individual_prices = array();
        $not_assessed_individual_prices = array();
        $assessed_individuals = array();
        $not_assessed_individuals = array();


        foreach ($this->population as $individual) {
            $experts = $this->respondToTheQuestion($individual);

            if ($this->isExpertsSolvesTheTask($experts)) {
                $a++;
                $assessed_individuals[] = $i;
                $individual_price = $this->individualPrice($experts);
                $assessed_individual_prices[] = $individual_price;
            } else {
                $b++;
                $not_assessed_individuals [] = $i;
                $this->population[$i] = array();
                $individual_price = $this->individualPrice($experts);
                $not_assessed_individual_prices[] = $individual_price;
//                unset($this->population[$i]);
//                unset($individual);
            }

            $i++;
        }
        if (count($not_assessed_individuals) < POPULATION_SIZE / 10) {

            $worsts = $this->needDie($not_assessed_individuals, $assessed_individual_prices);
            $not_assessed_individuals = array_merge($not_assessed_individuals, $worsts);
        }
//        echo "Ціни особин \n";
//        $this->outArr($individual_prices);
//        print_r($individual_prices);

        echo "Задачі виконали $a популяції\n";
        echo "Задачі не виконали $b популяції\n";
//        echo "assessed_individuals\n";
//        foreach ($assessed_individuals as $expert) {
//            echo $expert . " ";
//        }
//        echo "\n";
//        echo "not_assessed_individuals \n";
//        foreach ($not_assessed_individuals as $expert) {
//            echo $expert . " ";
//        }
//        echo "\n";

        return array(
            'assessed'     => $assessed_individuals,
            'not_assessed' => $not_assessed_individuals,
            'assessed_prices'       => $assessed_individual_prices,
            'not_assessed_prices'       => $not_assessed_individual_prices
        );
    }


    /**
     * needDie
     * Міняє місцями 0 і 1
     *
     * @param   array (int)           - 0 або 1
     * @param   array (int)           - 0 або 1
     * @param   array (int)           - 0 або 1
     * @return  int           - 1 або 0
     * */
    private
    function needDie(
        array $not_assessed_individuals,
        array $individual_prices
    ) {
        $not_assessed_individuals_size = POPULATION_SIZE / 10;
        $need_die = $not_assessed_individuals_size - count($not_assessed_individuals);
        $worsts = array();
        $i = 0;
        echo $need_die . "\n";
        echo $not_assessed_individuals_size . "\n";
        echo count($not_assessed_individuals) . "\n";
        while ($i != $need_die) {
            $max_key = $this->maxKey($individual_prices);
            if (!in_array($max_key, $not_assessed_individuals)) {
                $worsts[] = $max_key;
                unset($individual_prices[$max_key]);
                $individual_prices = array_values($individual_prices);
                $i++;
            } else {
                unset($individual_prices[$max_key]);

//                $individual_prices = array_values($individual_prices);
            }
        }
//        echo "Need die \n";
//        $this->outArr($worsts);
//        echo "Ціни найгірших\n";
        return $worsts;
    }

    /**
     * bestIndividuals
     * Ключі найдешевших особин
     *
     * @param array (int)    - ціни всіх особин що дали заключення по всіх питтаннях
     * @param array (int)    - особини що не дали заключення по всім питанням
     * @return  array (int)  - індекси найкращих особин серед тих хто дав заключення по всіх питаннях
     * */
    private
    function bestIndividuals(
        $individual_prices,
        $not_assessed_individuals
    ) {
        $need_best = count($not_assessed_individuals);
        $bests = array();
        $i = 0;
        $j = 0;
            while ($i != $need_best) {
                $min_key = $this->minKey($individual_prices);
                if (!in_array($min_key, $not_assessed_individuals) && !in_array($min_key, $bests)) {
                    $bests[] = $min_key;
                    unset($individual_prices[$min_key]);
                    $individual_prices = array_values($individual_prices);
                    $i++;
                } else {
                    unset($individual_prices[$min_key]);

//                $individual_prices = array_values($individual_prices);
                }
                $j++;

                if(empty($individual_prices))
                {
                    break;
                }

            }
//        echo "individual_prices $j \n";
//        print_r($individual_prices);
//        echo "bests \n";
//        print_r($bests);

        if(count($individual_prices) < $need_best ) {
            $need_randomise = $need_best - count($bests);
        }
        else{
            $need_randomise = 0;
        }
//        if (!empty($bests)) {
//            echo "Найдешевше коштують особини:\n";
//
//            print_r($bests);
//        }
//        else{
//            echo "Жопа\n";
//        }

        return array(
            'bests' => $bests,
            'need_random' =>  $need_randomise
        );
    }


    /**
     * maxKey
     * Ключ найдешевшої особини
     *
     * @param array (int) $individual_prices - ціни всіх особин що дали заключення по всіх питтаннях
     * @return  array (int) $max_key             - індекс особини з найменшою ціною
     * */
    private
    function maxKey(
        $individual_prices
    ) {
//        echo "MAX ". max ($individual_prices)."\n";
//        print_r($individual_prices);
        $max_key = array_search(max($individual_prices), $individual_prices);
        return $max_key;
    }

    /**
     * replace
     * Міняє місцями 0 і 1
     *
     * @param   int - 0 або 1
     * @return  int           - 1 або 0
     * */
    private
    function replace(
        $num
    ) {
        switch ($num) {
            case 1:
                $num = 0;
                break;
            case 2:
                $num = 1;
        }


        return $num;
    }


    /**
     * mutation
     * Зміна одного випадкового гена (запуск функції випадковий)
     *
     * @param array (int)   - одна особина популяції ( один з згенерованих нащадків )
     * @return  array (int) - одна особина популяції після проведення мутації( один з згенерованих нащадків )
     * */
    private
    function mutation(
        array $individual
    ) {
        echo "mutation start \n";
        $this->outArr($individual);
        $mutation_gen = mt_rand(0, $this->expertsNum - 1);
        echo "mutation gen $mutation_gen\n";
        $individual[$mutation_gen] = $this->replace($individual[$mutation_gen]);
        echo "mutation end \n";
        $this->outArr($individual);
        $experts = $this->respondToTheQuestion($individual);
        echo "Price " . $this->individualPrice($experts) . "\n";
        return $individual;
    }


    /**
     * selection
     * Зміна одного випадкового гена для покращення результату(запуск функції випадковий)
     *
     * @param   array (int) - одна особина популяції ( один з згенерованих нащадків )
     * @return  array (int) - одна особина популяції після проведення селекції( один з згенерованих нащадків )
     * */
    private
    function selection(
        array $individual
    ) {

        $experts = $this->respondToTheQuestion($individual);
        if ($this->isExpertsSolvesTheTask($experts)) {
            echo "start selection\n";
            $this->outArr($individual);
            echo "Price " . $individual_price_to_end = $this->individualPrice($experts) . "\n";
            foreach ($individual as &$item) {
                $individual_price_to_start = $this->individualPrice($experts);
                $item = $this->replace($item);
                $experts = $this->respondToTheQuestion($individual);
                $individual_price_to_end = $this->individualPrice($experts);
                if ($individual_price_to_start <= $individual_price_to_end) {
                    continue;
                } else {
                    break;
                }

            }
            echo "end selection\n";
            $this->outArr($individual);
            echo "Price " . $individual_price_to_end = $this->individualPrice($experts) . "\n";
            return $individual;
        } else {
            echo "Ця особина не життездатна. Провести селекцію неможливо !!! \n";
        }
    }

    /**
     * reanimation
     * Зміна випадкових генів для "оживлення" нащадка (запуск функції випадковий)
     *
     * @param array (int)   - одна особина популяції ( один з згенерованих нащадків )
     * @return  array (int) - одна особина популяції після проведення реанімації( один з згенерованих нащадків )
     * */
    private
    function reanimation(
        array $individual
    ) {
        $experts = $this->respondToTheQuestion($individual);
        $this->outArr($experts);
        if (!$this->isExpertsSolvesTheTask($experts)) {
            $reanimation_gen = mt_rand(0, $this->expertsNum - 1);
            $individual[$reanimation_gen] = $this->replace($individual[$reanimation_gen]);
            $experts = $this->respondToTheQuestion($individual);
            if ($this->isExpertsSolvesTheTask($experts)) {
                echo "Реанімація прошла успішно. Тепер ця особина життездатна !!!";
                return $individual;
            } else {
                echo "Реанімація провалилася/ Особина вимре при формуванні наступної популяції\n";
            }
        } else {
            echo "Ця особина життездатна. В реанімації немає сенсу !!! \n";
        }
        return $individual;
    }


    /**
     * createСhild
     * Створення нових особин на заміну тим що вимерли
     *
     * @param array (int)   - індекси найкращих особин серед тих хто дав заключення по всіх питаннях
     * @return  array (int) - закінчений масив нових дочірніх особин (створення розбиттям найкраших особин по точкам схрещування)
     * */
    private
    function createСhild(
        array $bests , $need_more
    ) {
        if (!empty($bests)) {
            $childrens = array();
            for ($i = 0; $i < count($bests); $i++) {
                $childrens[] = array();
            }

            $bests_count = count($bests);
            if ($this->expertsNum <= count($bests))
            {
                $bests_many =  array_chunk($bests,$this->expertsNum-1);
                $k = 0;
                foreach ($bests_many as $item)
                {
                    $crossing_points = $this->CrossingPoints($item);
                    $item_count = count($item);

                    for ($i = $k;$i < $item_count ; $i++) {
                        $need_array = $this->population[$bests[$i]];
                        $spliced_array = $this->individualSplice($need_array, $crossing_points, $item, $i);
                        $childrens = $this->individualMerge($i, $item_count , $childrens, $spliced_array);
                        $k++;
                        echo "i $i\n";

                    }
                }
            }
            else{
                $crossing_points = $this->CrossingPoints($bests);

                for ($i = 0; $i < $bests_count; $i++) {
                    $need_array = $this->population[$bests[$i]];
                    $spliced_array = $this->individualSplice($need_array, $crossing_points, $bests, $i);
                    $childrens = $this->individualMerge($i, $bests_count, $childrens, $spliced_array);

                }
            }

            if ($need_more > 0)
            {
                $random_child = $this->populationRandomise($need_more);
                echo "random_child\n";
                print_r($random_child);
                $childrens = array_merge($childrens, $random_child);

            }


            echo "Нові потомки\n";
            foreach ($childrens as $children) {
                $this->outArr($children);
            }
//            print_r($childrens);
            return $childrens;
        }

    }


    /**
     * output_population
     * Вивід масиву в лінію
     * @param
     * */
    private
    function output_population()
    {
        echo "Поточна популяція:\n";
        for ($i = 0; $i < count($this->population); $i++) {
            if ($i < 9) {
                echo "Особина номер " .
                    ($i + 1) . "  |";
            } else {
                echo "Особина номер " .
                    ($i + 1) . " |";
            }
            if (isset($this->population[$i])) {
                for ($j = 0; $j < count($this->population[$i]); $j++) {
                    echo $this->population[$i][$j] . "| ";
                }
            }
            echo "\n";
        }
//        print_r($this->population);
    }


    /**
     * respondToTheQuestion
     * Визначае експертів що є в особині і здатні відповідати на запитання
     *
     * @param array (int)    - вся особина
     * @return  array (int) - індекси експертів що дали заключення
     * */
    private
    function respondToTheQuestion(
        $individual
    ) {
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

    private
    function individualPrice(
        $experts
    ) {
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
     * @param array (int) $individual_prices - ціни всіх особин що дали заключення по всіх питтаннях
     * @return  array (int) $min_key             - індекс особини з найменшою ціною
     * */
    private
    function minKey(
        $individual_prices
    ) {
//        print_r($individual_prices);
        $min_key = array_search(min($individual_prices), $individual_prices);
        return $min_key;
    }


    /**
     * populationRandomise
     * Ствоенння випадкової популяції
     *
     * @param int - розмір популяції (кількість питаннь)
     * @return int - розмір популяції (кількість питаннь)
     * */
    private
    function populationRandomise(
        $populationSize
    ) {
        $population = array();
        for ($i = 0; $i < $populationSize; $i++) {
            $single = array();
            for ($j = 0; $j < $this->expertsNum; $j++) {
                $single[] = mt_rand(0, 1);
            }
            $population [] = $single;
        }

        return $population ;
    }


    /**
     * outArr
     * Вивід одновимірного масиву в лінію
     *
     * @param array (int) - масив для виводу
     * */
    private
    function outArr(
        array $arr
    ) {
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

    private
    function CrossingPoints(
        $bests
    ) {
        $crossing_points = array();
        $quantity = count($bests);
        while (count($crossing_points) != $quantity - 1) {
            $crossing_point = mt_rand(1, $this->expertsNum - 1);
            if (!in_array($crossing_point, $crossing_points)) {
                $crossing_points[] = $crossing_point;
            }
        }
        $crossing_points[] = 0;
//        echo "Точки схрещування:\n";
        rsort($crossing_points);
//        $this->outArr($crossing_points);

        return $crossing_points;
    }


    /**
     * individualSplice
     * Ділить масив відносно точок схрещування
     *
     * @param array (int)   - точки схрещування
     * @param array (int)   - індекси найкращих особин серед тих хто дав заключення по всіх питаннях
     * @param int - індекс зовнішнього масиву
     * @return  array (int) - масив поділений відносно точок схрещування
     * */
    private
    function individualSplice(
        array $needArray,
        $crossing_points,
        $bests,
        $i
    ) {
        $spliced_array = array();
        for ($j = 0; $j < count($crossing_points); $j++) {
//            $old_spliced_array = $this->population[$bests[$i]];
            $spliced_array[] = array_splice($needArray, $crossing_points[$j]);
        }
        return $spliced_array;
    }


    /**
     * individualMerge
     * Проводить ітерацію по злиттю із individualSplice() кожної з найкращих особин
     *
     * @param int - індекс зовнішнього масиву
     * @param int - кількість найкращих особин (визначається на основі того скілки особин вимерли)
     * @param array (int)   - масив нових дочірніх особин на момент злиття(створення розбиттям найкраших особин по точкам схрещування)
     * @param array (int)   - масив вже розбитої відносно точок схрещування особини
     * @return  array (int) - масив нових дочірніх особин на момент злиття(створення розбиттям найкраших особин по точкам схрещування)
     * */
    private
    function individualMerge(
        $i,
        $bests_count,
        $children,
        $spliced_array
    ) {
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
