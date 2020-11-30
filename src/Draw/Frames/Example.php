<?php


namespace CliEngine\Draw\Frames;


class Example
{
    public $x = 2;
    public $y = 2;

    public function getTemplate()
    {
        $block = [
            '** Test ********',
            '* abababababab *',
            '* 😝😝😝😝😝😝 *',
            '****************',
        ];
        return $block;
    }

    public function getWidth()
    {
        return strlen($this->getTemplate()[0]);
    }

    public function getHeight()
    {
        return count($this->getTemplate());
    }
}