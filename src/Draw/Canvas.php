<?php

namespace CliEngine\Draw;

use CliEngine\Draw\Frames\Background;
use CliEngine\IO\Keyboard;
use CliEngine\IO\Terminal;
use CliEngine\Draw\Frames\Example;

/**
 * Отвечает за контроль отрисовки
 */
class Canvas
{
    protected $terminal;
    protected $keyboard;
    protected $unicode;
    protected $isNeedDraw = true;

    // участки для отрисовки
//    protected $frames = [];

    public function __construct(Terminal $t)
    {
        $this->unicode = new Unicode();
        $this->keyboard = new Keyboard();
        $this->terminal = $t;
    }

    public function merge($base, $b)
    {
        $mask = $b->getTemplate();

        $result = [];

        $row = 0;
        while ($row < $this->terminal->rows) {
            $col = 0;
            $line = '';
            while ($col < $this->terminal->cols) {
                if (
                    $row >= $b->y && $row-1 <= $b->getHeight()
                    &&
                    $col >= $b->x && $col-1 <= $b->getWidth()
                ) {
                    $char = mb_substr($mask[$row - $b->y], $col - $b->x, 1);
                    $tmp = iconv(mb_detect_encoding($char, mb_detect_order(), true), "UTF-8", $char);
                    $line .= $tmp;
                } else {
                    $line .= mb_substr($base[$row], $col, 1);
                }
                $col++;
            }
            $row++;
            $result[] = $line;
        }

        return $result;
    }

    public function render()
    {

        $base = new Background($this->terminal);
        $frame = new Example();

        $result = $this->merge($base->getTemplate(), $frame);

        var_dump($result);
        die();
        return implode("\n", $result);
    }

    public function live()
    {
        // чтобы ожидание ввода не блокировало вывод
        stream_set_blocking(STDIN, false);
        // выключаем вывод на экран пользовательского ввода
        readline_callback_handler_install('', function () {
        });
        // скрываем курсор и очищаем экран
        $this->terminal->cursor('hide');
        $this->terminal->erase('screen');
        // чтобы область отрисовки всегда была в верхнем левом углу
        $this->terminal->cursor('up', $this->terminal->rows);

        while (true) {
            if ($string = fgets(STDIN)) {
                $this->keyboard->handle($string);
                $this->isNeedDraw = true;
            }
            if ($this->isNeedDraw) {
                $this->terminal->out($this->render());
                $this->terminal->cursor('up', $this->terminal->rows);
                $this->isNeedDraw = false;
            }
            usleep(1000); // 1 мс
        }
    }
}