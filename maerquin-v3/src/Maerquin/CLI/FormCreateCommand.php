<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\CLI;

use Override;
use SvenHK\Maerquin\Model\Form;
use SvenHK\Maerquin\Repository\FormRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FormCreateCommand extends Command
{
    public function __construct(
        private readonly FormRepository $formRepository,
    ) {
        parent::__construct();
    }

    #[Override]
    protected function configure(): void
    {
        $this
            ->setName('maerquin:form:create')
            ->setDescription('Creates an empty form')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the form');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $form = Form::create((string)$input->getArgument('name'));
        $this->formRepository->save($form);

        $output->writeln(sprintf('<info>Form "%s" created with id %s</info>', $form->getName(), $form->getId()));

        return self::SUCCESS;
    }
}
