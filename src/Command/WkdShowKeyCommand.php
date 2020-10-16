<?php

namespace App\Command;

use App\Handler\WkdHandler;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WkdShowKeyCommand extends Command
{
    /**
     * @var WkdHandler
     */
    private $handler;

    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(ObjectManager $manager, WkdHandler $handler)
    {
        $this->handler = $handler;
        $this->repository = $manager->getRepository('App:User');
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:users:wkd:show-key')
            ->setDescription('Show WKD key of user')
            ->addArgument(
                'email',
                InputOption::VALUE_REQUIRED,
                'email address of the user');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // parse arguments
        $email = $input->getArgument('email');

        // Check if user exists
        $user = $this->repository->findByEmail($email);
        if (null === $user) {
            throw new \RuntimeException('User not found: '.$email);
        }

        // Get fingerprint of the key
        $wkdKey = $this->handler->getKey($user);

        $output->writeln(sprintf('WKD key for user %s: %s', $user->getEmail(), $wkdKey->getFingerprint()));
    }
}
