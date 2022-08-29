<?php

namespace CliEngine\Draw\Frames;

use CliEngine\Draw\Unicode;
use CliEngine\IO\Terminal;

class Background implements IFrame
{
    public int $x = 0;
    public int $y = 0;
    public Terminal $terminal;
    public string $symbol;

    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
        $unicode = new Unicode();
        $symbols = $unicode->getDiap('Блочные элементы');
        $this->symbol = $symbols[array_rand($symbols)];
    }

    public function getTemplate(): array
    {
        $block = [];
        $row = 0;
        while ($row < $this->terminal->rows) {
            $col = 0;
            $line = '';
            while ($col < $this->terminal->cols) {
                $line .= $this->symbol;
                $col++;
            }
            $row++;
            $block[] = $line;
        }
        return $block;
    }

    public function getWidth(): int
    {
        return mb_strlen($this->getTemplate()[0]);
    }

    public function getHeight(): int
    {
        return count($this->getTemplate());
    }
}