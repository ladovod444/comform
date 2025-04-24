<?php

#parse("PHP File Header.php")

declare(strict_types=1);

#if (${NAMESPACE})
namespace ${NAMESPACE};
#end

#set( $CONTEXT = $NAME.replace("Command", "") )
#set( $DOMAIN = $CONTEXT.replaceAll("([a-z])([A-Z]+)", "$1:$2").toLowerCase() )

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'baks:$DOMAIN',
    description: 'Описание комманды'
)]
class $NAME extends Command
{
    public function __construct()
    {
        parent::__construct();
    }
    
    protected function configure(): void
    {
        ${DS}this->addArgument('argument', InputArgument::OPTIONAL, 'Описание аргумента');
    }

    protected function execute(InputInterface ${DS}input, OutputInterface ${DS}output): int
    {
        ${DS}io = new SymfonyStyle(${DS}input, ${DS}output);

        ${DS}io->success('baks:$DOMAIN');

        return Command::SUCCESS;
    }
}
