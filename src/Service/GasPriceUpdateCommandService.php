<?php

namespace App\Service;

use Safe;

class GasPriceUpdateCommandService
{
    public function __construct(
        private readonly string $gasPricePath,
        private readonly string $gasPriceJsonName,
    ) {
    }

    public function invoke(): void
    {
        if (!FileSystemService::exist($this->gasPricePath, $this->gasPriceJsonName)) {
            throw new \Exception('json gas prices dont exist.');
        }

        $file = fopen("$this->gasPricePath/$this->gasPriceJsonName", 'r');

        $content = fread($file, filesize("$this->gasPricePath/$this->gasPriceJsonName"));
        fclose($file);

        $data = Safe\json_decode($content, true);
    }
}
