<?php

namespace App\Command;

use App\Service\FileSystemService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:gas-price:download',
)]
class GasPriceDownloadCommand extends Command
{
    public function __construct(
        private string $gasPriceUrl,
        private string $gasPricePath,
        private string $gasPriceName,
        private string $gasPriceJsonName,
    ) {
        parent::__construct(self::getDefaultName());
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Step 1 : deleting old files');
        FileSystemService::delete(FileSystemService::find($this->gasPricePath, "%\.(xml)$%i"));
        FileSystemService::delete($this->gasPricePath, $this->gasPriceName);
        FileSystemService::delete($this->gasPricePath, $this->gasPriceJsonName);

        $io->title('Step 2 : downloading gas prices zip');
        FileSystemService::download($this->gasPriceUrl, $this->gasPriceName, $this->gasPricePath);

        if (false === FileSystemService::exist($this->gasPricePath, $this->gasPriceName)) {
            throw new \Exception('Zip file cant be found.');
        }

        $io->title('Step 3 : unziping gas prices zip');
        if (false === FileSystemService::unzip(sprintf('%s%s', $this->gasPricePath, $this->gasPriceName), $this->gasPricePath)) {
            throw new \Exception('Zip file cant be unzip.');
        }

        FileSystemService::delete($this->gasPricePath, $this->gasPriceName);

        if (null === $xmlPath = FileSystemService::find($this->gasPricePath, "%\.(xml)$%i")) {
            throw new \Exception('Xml file cant be found.');
        }

        $io->title('Step 4 : create new gas prices json file');
        $elements = simplexml_load_file($xmlPath);
        $json = json_encode($elements);
        $data = json_decode($json, true);

        $file = fopen("$this->gasPricePath/$this->gasPriceJsonName", 'w') or exit('Cant open the file.');
        fwrite($file, json_encode($data['pdv'] ?? []));
        fclose($file);

        $io->title('Step 5 : deleting xml gas prices file');
        FileSystemService::delete(FileSystemService::find($this->gasPricePath, "%\.(xml)$%i"));

        return Command::SUCCESS;
    }
}
