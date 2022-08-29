<?php

namespace CliEngine\Draw\Frames;

interface IFrame
{
    public function getTemplate(): array;
    public function getWidth(): int;
    public function getHeight(): int;
}