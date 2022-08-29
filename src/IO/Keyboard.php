<?php

namespace CliEngine\IO;

/**
 * Обработка нажатий клавиш
 */
class Keyboard
{
    public array $control = [
        27 => 'ESC',
        9 => 'TAB',
        32 => 'SPACE',
        10 => 'ENTER',
        '1b5b5a' => 'RTAB',
        '1b5b41' => 'UP',
        '1b5b42' => 'DOWN',
        '1b5b43' => 'RIGHT',
        '1b5b44' => 'LEFT',
    ];

    public function handle(string $key, array $frames): void
    {
        // многобайтные последовательности
        $key = (strlen($key) > 1) ? bin2hex($key) : ord($key);

        $nameKey = $this->control[$key] ?? chr($key);
        $ctrl = $frames['example'];

        match ($nameKey) {
            'ESC'   => exit,
            'LEFT'  => $ctrl->x -= 1,
            'RIGHT' => $ctrl->x += 1,
            'UP'    => $ctrl->y -= 1,
            'DOWN'  => $ctrl->y += 1,
        };
    }
}