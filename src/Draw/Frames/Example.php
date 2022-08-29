<?php

namespace CliEngine\Draw\Frames;

class Example implements IFrame
{
    public int $x = 2;
    public int $y = 2;

    public function getTemplate(): array
    {
        return [
            '** Test ********',
            '* abababababab *',
            '* 😝😝😝😝😝😝 *',
            '****************',
        ];
    }

    public function getWidth(): int
    {
        return strlen($this->getTemplate()[0]);
    }

    public function getHeight(): int
    {
        return count($this->getTemplate());
    }
}