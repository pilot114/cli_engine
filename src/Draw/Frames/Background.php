<?php


namespace CliEngine\Draw\Frames;

use CliEngine\Draw\Unicode;
use CliEngine\IO\Terminal;

class Background
{
    public $x = 0;
    public $y = 0;
    public $terminal;

    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }

    public function getTemplate()
    {
        $unicode = new Unicode();
        $symbols = $unicode->getDiap('Блочные элементы');
        $symbol = $symbols[array_rand($symbols)];

        $block = [];
        $row = 0;
        while ($row < $this->terminal->rows) {
            $col = 0;
            $line = '';
            while ($col < $this->terminal->cols) {
                $line .= $symbol;
                $col++;
            }
            $row++;
            $block[] = $line;
        }
        return $block;
    }

    public function getWidth()
    {
        return mb_strlen($this->getTemplate()[0]);
    }

    public function getHeight()
    {
        return count($this->getTemplate());
    }
}