<?php

namespace App\Command;

use App\Entity\Note;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';
    protected static $defaultDescription = 'Import existing notes';
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('filename', InputArgument::OPTIONAL, 'Import file name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filename = $input->getArgument("filename");
        $lines = file($filename);
        $count = 0;
        foreach ($lines as $line) {
            $data = json_decode($line, true);
            $note = (new Note)
                ->setAccessCount($data["AccessCount"])
                ->setCreatedAt($this->makeTime($data, "CreatedAt"))
                ->setUpdatedAt($this->makeTime($data, "UpdatedAt"))
                ->setAccessedAt($this->makeTime($data, "AccessedAt"))
                ->setCategory($data["Category"])
                ->setSequence($data["Sequence"])
                ->setTitle($data["Title"])
                ->setContent($data["Content"])
            ;
            $this->em->persist($note);
            ++$count;
            foreach ($data["Tags"] as $tag) {
                $tag = (new Tag)
                    ->setNote($note)
                    ->setName($tag);
                $this->em->persist($tag);
            }
        }
        $this->em->flush();
        $io->success("$count Notes created");
        return 0;
    }

    private function makeTime(array $data, string $field)
    {
        if (empty($data[$field])) {
            return null;
        }

        $timeString = $data[$field]["\$date"];
        dump($timeString);

        if (empty($timeString)) {
            return null;
        }
        $time = \DateTimeImmutable::createFromFormat(DATE_ATOM, $timeString);
        if ($time === false) {
            $time = \DateTimeImmutable::createFromFormat(\DateTimeInterface::RFC3339_EXTENDED, $timeString);
        }
        return $time;
    }
}
