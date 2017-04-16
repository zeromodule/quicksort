<?php
namespace Tests\AppBundle\Service;

use AppBundle\Service\QuickSorter;
use PHPUnit\Framework\TestCase;

class QuickSorterTest extends TestCase
{
    /**
     * @dataProvider sortProvider
     * @param $inputArray
     */
    public function testSort($inputArray)
    {
        $quickSorter = new QuickSorter();

        $numberIndexedResultArray = array_values( $quickSorter->sort($inputArray) );

        foreach ($numberIndexedResultArray as $key => $value) {

            if (isset($numberIndexedResultArray[$key + 1])) {

                $this->assertTrue($value <= $numberIndexedResultArray[$key + 1], json_encode($numberIndexedResultArray));
            }
        }
    }

    /**
     * @param array $inputArray
     * @param int $left
     * @param int $right
     * @dataProvider getPivotKeyProvider
     */
    public function testGetPivotKey(array $inputArray, int $left, int $right)
    {
        $quickSorter = new QuickSorter();
        $inputArray = array_values($inputArray);

        if (empty($inputArray)) {
            // check exception on empty array
            $this->expectException(\InvalidArgumentException::class);
            $emptyArray = [];
            $quickSorter->getPivotKey($emptyArray, $left, $right);
            return;
        }

        if (!isset($inputArray[$left]) || !isset($inputArray[$right])) {
            $this->expectException(\OutOfRangeException::class);
            $quickSorter->getPivotKey($inputArray, $left, $right);
            return;
        }

        if ($left > $right) {
            $this->expectException(\InvalidArgumentException::class);
            $quickSorter->getPivotKey($inputArray, $left, $right);
            return;
        }

        // check that key really exists
        $pivotKey = $quickSorter->getPivotKey($inputArray, $left, $right);

        $this->assertTrue(isset($inputArray[$pivotKey]), "left: {$left}, right: {$right}, pivotKey: {$pivotKey}");
    }

    /**
     * @param $inputArray
     * @param $indexA
     * @param $indexB
     *
     * @dataProvider swapProvider
     */
    public function testSwap(array $inputArray, $indexA, $indexB)
    {
        $quickSorter = new QuickSorter();

        if (isset($inputArray[$indexA]) && isset($inputArray[$indexB]) && $indexA !== $indexB) {
            $a = $inputArray[$indexA];
            $b = $inputArray[$indexB];
            $quickSorter->swap($inputArray, $indexA, $indexB);
            $this->assertTrue($inputArray[$indexA] === $b && $inputArray[$indexB] === $a);
        } else {
            $this->expectException(\InvalidArgumentException::class);
            $quickSorter->swap($inputArray, $indexA, $indexB);
        }
    }

    public function sortProvider()
    {
        $randomIntegersData = [];

        for ($i = 0; $i <= 100; $i++) {
            $randomNumbersArray = range(rand(0, 1000), rand(1001, 2000));
            shuffle($randomNumbersArray);
            $randomIntegersData[] = [$randomNumbersArray];
        }

        return array_merge($randomIntegersData, [
            [[]],
            [[0]],
            [[-1]],
            [[3,2,1]],
            [[-1,0,1]],
            [[2,2,2,2]],
            [[1,2,3,4,5,7,6]],
            [[-100, 0.1, 3402934, 23.5, 9, 99]],
            [[-9,-8,-7,-6,-5]],
            [[0.01,0.1,0.02,0.03,0.04]]
        ]);
    }

    public function swapProvider()
    {
        return [
            [[], 0, 0],
            [[0], 2, 0],
            [[3,2,1], 1, 2],
            [[-1,0,1], 3, 10],
            [[2,2,2,2], 1, 2],
            [[1,2,3,4,5,7,6], 3, 3],
            [['a' => 10, 'b' => 100], 'a', 'b'],
            [['c' => 10, 'd' => 100], 'c', 'c'],
        ];
    }

    public function getPivotKeyProvider()
    {
        return [
            [[], 0, 0],
            [[0], 2, 0],
            [[3,2,1], 1, 2],
            [[-1,0,1], 3, 10],
            [[2,2,2,2], 1, 2],
            [[1,2,3,4,5,7,6], 3, 3],
            [[1,2,3,4,5,7,6], 1, 4],
            [['a' => 10, 'b' => 100], 0, 1],
            [['c' => 10, 'd' => 100], 1, 0],
        ];
    }
}
