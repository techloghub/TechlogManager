<?php

namespace BCC\ResqueBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use BCC\ResqueBundle\Resque;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StopWorkerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('bcc:resque:worker-stop')
            ->setDescription('Stop a bcc resque worker')
            ->addArgument('id', InputArgument::OPTIONAL, 'Worker id')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Should kill all workers')
        ;
    }
    
    private function getLikelyWorkers($availableWorkers, $workerId)
    {
        $workers = array();
        foreach ($availableWorkers as $worker) {
            if (strpos($worker->getId(), $workerId) !== false) {
                $workers[] = $worker;
                
            }
        }
        return $workers;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resque = $this->getContainer()->get('bcc_resque.resque');

        if ($input->getOption('all')) {
            $workers = $resque->getWorkers();
        } else {
            $workerId = $input->getArgument('id');
            $worker = $resque->getWorker($workerId);
            if ($worker) {
                $workers = array($worker);
            } else {
                $availableWorkers = $resque->getWorkers();
                $workers = $this->getLikelyWorkers($availableWorkers, $workerId);
            }
            
            if (!$workers) {
                $availableWorkers = $resque->getWorkers();
                if (!empty($availableWorkers)) {
                    $output->writeln('<error>You need to give an existing worker.</error>');
                    $output->writeln('Running workers are:');
                    foreach ($resque->getWorkers() as $worker) {
                        $output->writeln($worker->getId());
                    }
                } else {
                    $output->writeln('<error>There is no running worker.</error>');
                }

                return 1;
            }
        }

        foreach ($workers as $worker) {
            $output->writeln(\sprintf('Stopping %s...', $worker->getId()));
            $worker->stop();
        }

        return 0;
    }
}
