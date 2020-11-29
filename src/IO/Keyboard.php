<?php

namespace CliEngine\IO;

/**
 * Обработка нажатий клавиш
 */
class Keyboard
{
    public $control = [
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

    public function handle($key)
    {
        // многобайтные последовательности
        if (strlen($key) > 1) {
            $key = bin2hex($key);
        } else {
            $key = ord($key);
        }

        $nameKey = $this->control[$key] ?? chr($key);

        if ($nameKey === 'ESC') exit;

        if ($nameKey === 'ESC') exit;
    }
}