<?php
// src/SeguridadBundle/Command/SessionCollectorCommand.php
namespace SeguridadBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use SeguridadBundle\Entity\Logs;

class SessionCollectorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('seguridad:sessionCollector')
            ->setDescription('Termina las sesiones por inactividad')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Who do you want to greet?'
            )
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        
        $users = $em->getRepository('SeguridadBundle:Usuario')->findBy(array('logueado' => 1));
        foreach($users as $user)
        {
            $intervalo=$user->getUltimaOperacion()->diff(new \DateTime());
            $minutes = $intervalo->days * 24 * 60;
            $minutes += $intervalo->h * 60;
            $minutes += $intervalo->i;
            if ($minutes>30){
                $em->persist(new Logs($user,'0.0.0.0/0','a-logout'));
                $user->setLogueado(false);
                
            }
            $output->writeln($minutes);
        }
        $em->flush();
    }
}