<?php

namespace BCC\ResqueBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StartScheduledWorkerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('bcc:resque:scheduledworker-start')
            ->setDescription('Start a bcc scheduled resque worker')
            ->addOption('foreground', 'f', InputOption::VALUE_NONE, 'Should the worker run in foreground')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force creation of a new worker if the PID file exists')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pidFile=$this->getContainer()->get('kernel')->getLogDir().'/bcc_resque_scheduledworker.pid';
        if (file_exists($pidFile) && !$input->getOption('force')) {
            throw new \Exception('PID file exists - use --force to override');
        }

        if (file_exists($pidFile)) {
            unlink($pidFile);
        }

        $env = array(
            'APP_INCLUDE' => $this->getContainer()->getParameter('bcc_resque.resque.vendor_dir').'/autoload.php',
            'VVERBOSE'    => 1,
            'RESQUE_PHP' => $this->getContainer()->getParameter('bcc_resque.resque.vendor_dir').'/chrisboulton/php-resque/lib/Resque.php',
        );

        $prefix = $this->getContainer()->getParameter('bcc_resque.prefix');
        if (!empty($prefix)) {
            $env['PREFIX'] = $this->getContainer()->getParameter('bcc_resque.prefix');
        }

        $redisHost = $this->getContainer()->getParameter('bcc_resque.resque.redis.host');
        $redisPort = $this->getContainer()->getParameter('bcc_resque.resque.redis.port');
        $redisDatabase = $this->getContainer()->getParameter('bcc_resque.resque.redis.database');
        if ($redisHost != null && $redisPort != null) {
            $env['REDIS_BACKEND'] = $redisHost.':'.$redisPort;
        }
        if (isset($redisDatabase)) {
            $env['REDIS_BACKEND_DB'] = $redisDatabase;
        }

        $phpCmd = $this->getContainer()->getParameter('php.cmd');
        $workerCommand = $phpCmd.' '.$this->getContainer()->getParameter('bcc_resque.resque.vendor_dir').'/chrisboulton/php-resque-scheduler/resque-scheduler.php';

        if (!$input->getOption('foreground')) {
            $logFile = $this->getContainer()->getParameter(
                'kernel.logs_dir'
            ) . '/resque-scheduler_' . $this->getContainer()->getParameter('kernel.environment') . '.log';
            $workerCommand = 'nohup ' . $workerCommand . ' > ' . $logFile .' 2>&1 & echo $!';
        }

        $process = new Process($workerCommand, null, $env);

        $output->writeln(\sprintf('Starting worker <info>%s</info>', $process->getCommandLine()));

        if ($input->getOption('foreground')) {
            $process->run(function ($type, $buffer) use ($output) {
                    $output->write($buffer);
                });
        }
        // else we recompose and display the worker id
        else {
            $process->run();
            $pid = \trim($process->getOutput());
            if (function_exists('gethostname')) {
                $hostname = gethostname();
            } else {
                $hostname = php_uname('n');
            }
            $output->writeln(\sprintf('<info>Worker started</info> %s:%s', $hostname, $pid));
            file_put_contents($pidFile,$pid);
        }
    }
}
