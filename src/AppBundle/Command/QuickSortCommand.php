<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QuickSortCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('QuickSort')
            ->setDescription('Sorts input array and outputs result')
            ->addArgument('array', InputArgument::IS_ARRAY, 'Input array members (space splitted)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $quickSorter = $this->getContainer()->get('app.service.quick_sorter');
        $inputArray = $input->getArgument('array');

        $stringResult = implode(' ', $quickSorter->sort($inputArray));
        $output->writeln($stringResult);
    }

}
