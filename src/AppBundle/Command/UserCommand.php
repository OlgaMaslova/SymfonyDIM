<?php

namespace AppBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserCommand extends Command
{
    protected static $defaultName = 'app:create-user';
    protected function configure()
    {
        $this
            ->setDescription('Creates user.')
            ->addArgument('email', InputArgument::REQUIRED, 'Email og the user')
            ->addArgument('password', InputArgument::REQUIRED, 'The plain password of the user.')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        //Creates a user in database
        $output->writeln(sprintf('<info>Email: %s, Password: %s</info>', $email, $password));
    }

}