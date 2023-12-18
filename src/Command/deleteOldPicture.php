<?php

namespace App\Command;

use App\Repository\AdvertRepository;
use App\Repository\PictureRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:delete-old-picture',
    description: 'Delete old pictures.',
    aliases: ['app:del-old-pic'],
    hidden: false
)]
class deleteOldPicture extends Command
{
    private PictureRepository $pictureRepository;

    public function __construct( PictureRepository $pictureRepository)
    {
        parent::__construct();


        $this->pictureRepository = $pictureRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Delete old picture')
            ->setHelp('This command allows you to delete old picture by day.')
            ->addArgument('day', InputArgument::REQUIRED, 'number of days');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new OutputFormatterStyle('white', 'green', ['bold']);
        $output->getFormatter()->setStyle('success', $style);

        $style = new OutputFormatterStyle('white', 'red', ['bold']);
        $output->getFormatter()->setStyle('error', $style);

        $style = new OutputFormatterStyle('white', 'yellow', ['bold']);
        $output->getFormatter()->setStyle('warning', $style);
        $io = new SymfonyStyle($input, $output);

        $days = $input->getArgument('day');
        if (ctype_digit($days)) {
            $date = new \DateTimeImmutable();
            $date = $date->modify("-$days days");
            $oldPictures = $this->pictureRepository->getOldPicture($date);
            if ($oldPictures) {
                $deletedCount = 0;
                foreach ($oldPictures as $picture) {
                    if (!$picture->getAdvert()) {
                        $this->pictureRepository->deletePicture($picture);
                            $deletedCount++;
                        }

                    $io->success("$deletedCount pictures deleted.");
                    return Command::SUCCESS;
                }
            } else {
                $io->warning("No old pictures found.");
                return Command::FAILURE;
            }
        } else {
            $io->error("Invalid input for days.");
            return Command::INVALID;
        }
    }
}
