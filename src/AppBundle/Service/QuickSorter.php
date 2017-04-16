<?php
namespace AppBundle\Service;

use AppBundle\Contracts\Sorter;

class QuickSorter implements Sorter
{
    public function sort(array $input): array
    {
        if (count($input) > 1) {
            $this->quicksort($input, 0, count($input) - 1);
        }

        return $input;
    }

    /**
     * @param array $input
     * @param $startKey
     * @param $endKey
     * @param string $part
     */
    private function quicksort(array &$input, $startKey, $endKey, $part = "")
    {
        // не сортируем куски где 1 или менее элемент
        if (($endKey - $startKey) <= 0) {
            return;
        }

        // получаем опорный элемент
        $pivotKey = $this->getPivotKey($input, $startKey, $endKey);

        // располагаем элементы меньше опорного - слева, а больше - справа от него
        $meetingKey = $this->splitForTwoParts($input, $startKey, $endKey, $pivotKey, $part);

        // рекурсивно повторяем алгоритм для обеих групп "левая + опорный элемент" и "правая"
        $this->quicksort($input, $startKey, $meetingKey, "left");
        $this->quicksort($input, $meetingKey+1, $endKey, "right");
    }

    /**
     * Возвращает индекс "опорного" элемента, относительно которого будет происходить распределение элементов
     * Данная реализация возвращает СЛУЧАЙНЫЙ элемент для большей оптимизации
     *
     * @param array $input
     * @param int $left левый индекс-ограничитель выборки
     * @param int $right правый индекс-ограничитель выборки
     * @return mixed
     */
    public function getPivotKey(array $input, int $left, int $right)
    {
        $input = array_values($input);

        // пустой массив нам не помощник
        if (empty($input)) {
            throw new \InvalidArgumentException('Empty array no need sorting');
        }

        // пограничные элементы должны существовать
        if (!isset($input[$left]) || !isset($input[$right])) {
            throw new \OutOfRangeException('Pivot range out of array');
        }

        // левый индекс не может быть больше правого
        if ($left > $right) {
            throw new \InvalidArgumentException('Incorrect range definition');
        }

        return rand($left, $right);
    }

    /**
     * Заменяет элементы относительно опорного таким образом, что слева от него остаются элементы, которые меньше его,
     * а справа - которые больше.
     *
     * Реализация по Хоару.
     *
     * @param $input
     * @param $startKey
     * @param $endKey
     * @param $pivotKey
     *
     * @return int
     */
    public function splitForTwoParts(&$input, $startKey, $endKey, $pivotKey)
    {
        $pivot = $input[$pivotKey];

        // чтобы содержимое do было единообразным для любой позиции

        $left = $startKey - 1;
        $right = $endKey + 1;

        while (true) {

            /**
             * Идём слева направо и справа налево навстречу друг другу относительно опорного элемента,
             * пока не встретим ситуацию что элемент справа меньше опорного, а слева больше.
             * Тогда меняем их местами.
             */
            do {
                $left++;

            } while ($input[$left] < $pivot);

            do {
                $right--;

            } while ($input[$right] > $pivot);

            // если встретились, то меняемся местами, а если оба пришли к опорному - то работа закончена.

            if ($left >= $right) {
                return $right;
            }

            $this->swap($input, $left, $right);
        }
    }

    /**
     * @param $input
     * @param $left
     * @param $right
     */
    public function swap(&$input, $left, $right)
    {
        // по указанным индексам должны быть элементы
        if (!isset($input[$left]) || !isset($input[$right])) {
            throw new \InvalidArgumentException('Incorrect indexes for swap!');
        }

        // менять элемент сам на себя - бессмысленно
        if ($left === $right) {
            throw new \InvalidArgumentException('Swap indexes equals. Nothing to do.');
        }

        list($input[$left], $input[$right]) = [$input[$right], $input[$left]];
    }
}