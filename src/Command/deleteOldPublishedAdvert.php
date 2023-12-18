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
    name: 'app:delete-old-published-advert',
    description: 'Delete old published Adverts.',
    aliases: ['app:del-old-pub-adv'],
    hidden: false
)]
class deleteOldPublishedAdvert extends Command
{
    private AdvertRepository $advertRepository;
    private PictureRepository $pictureRepository;

    public function __construct(AdvertRepository $advertRepository, PictureRepository $pictureRepository)
    {
        parent::__construct();

        $this->advertRepository = $advertRepository;
        $this->pictureRepository = $pictureRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Delete old published Advert')
            ->setHelp('This command allows you to delete old published Advert by day.')
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
            $oldAdverts = $this->advertRepository->getOldPublishedAdvert($date);
            if ($oldAdverts) {
                $deletedCount = 0;
                foreach ($oldAdverts as $advert) {
                    if ($advert->getState() === 'published') {
                        foreach ($advert->getPictures() as $picture) {
                            $this->pictureRepository->deletePicture($picture);
                        }

                        $this->advertRepository->deleteAdvert($advert);
                        $deletedCount++;
                    }
                    $io->success("$deletedCount adverts deleted.");
                    return Command::SUCCESS;
                }
            } else {
                $io->warning("No old adverts found.");
                return Command::FAILURE;
            }
        } else {
            $io->error("Invalid input for days.");
            return Command::INVALID;
        }
    }
}
