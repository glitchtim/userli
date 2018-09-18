<?php

namespace AppBundle\Command;

use AppBundle\Entity\Voucher;
use AppBundle\Enum\Roles;
use AppBundle\Handler\SuspiciousChildrenHandler;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author louis@systemli.org
 */
class VoucherUnlinkCommand extends Command
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var SuspiciousChildrenHandler
     */
    private $handler;

    /**
     * VoucherUnlinkCommand constructor.
     *
     * @param SuspiciousChildrenHandler $handler
     */
    public function __construct(ObjectManager $manager, SuspiciousChildrenHandler $handler)
    {
        $this->manager = $manager;
        $this->handler = $handler;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('usrmgmt:voucher:unlink')
            ->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'dry run, without any changes')
            ->setDescription('Remove connection between vouchers and accounts after 3 months');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vouchers = $this->getVouchers();
        $suspiciousChildren = [];

        $output->writeln(
            sprintf('<INFO>unlink %d vouchers</INFO>', count($vouchers)),
            OutputInterface::VERBOSITY_VERBOSE
        );

        foreach ($vouchers as $voucher) {
            $user = $voucher->getInvitedUser();
            if (null !== $user) {
                $user->setInvitationVoucher(null);

                // check if user was suspicious and has redeemed codes
                $parent = $voucher->getUser();
                if ($parent->hasRole(Roles::SUSPICIOUS)) {
                    $suspiciousChildren[$user->getUsername()] = $parent->getUsername();
                }
            }

            $output->writeln(
                sprintf(
                    '%d: %s (%s)',
                    $voucher->getId(),
                    $voucher->getCode(),
                    $voucher->getRedeemedTime()->format(\DateTime::W3C)
                ),
                OutputInterface::VERBOSITY_VERY_VERBOSE
            );
        }

        if (count($suspiciousChildren) > 0) {
            // output all children of suspicious users
            foreach ($suspiciousChildren as $child => $parent) {
                $output->writeln(
                    sprintf(
                        '<comment>Suspicious User %s has invited %s.</comment>',
                        $parent,
                        $child
                    ),
                    OutputInterface::VERBOSITY_VERBOSE
                );
            }

            // inform about suspicious children via mail
            $this->handler->sendReport($suspiciousChildren);
        }

        if (false === $input->getOption('dry-run')) {
            $this->manager->flush();
        }
    }

    /**
     * @return Voucher[]|array
     */
    private function getVouchers()
    {
        return $this->manager->getRepository('AppBundle:Voucher')
            ->createQueryBuilder('voucher')
            ->join('voucher.invitedUser', 'invitedUser')
            ->where('voucher.redeemedTime < :date')
            ->setParameter('date', new \DateTime('-3 months'))
            ->orderBy('voucher.redeemedTime')
            ->getQuery()
            ->getResult();
    }
}
