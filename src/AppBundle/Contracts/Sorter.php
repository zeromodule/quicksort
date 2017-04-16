<?php
namespace AppBundle\Contracts;

interface Sorter
{
    public function sort(array $inputArray) : array ;
}