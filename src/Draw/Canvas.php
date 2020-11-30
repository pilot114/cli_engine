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
    protected $back = null;
    protected $frames = [];

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
                    $row >= $b->y && $row+1 <= ($b->y + $b->getHeight())
                    &&
                    $col >= $b->x && $col+1 <= ($b->x + $b->getWidth())
                ) {
                    $line .= mb_substr($mask[$row - $b->y], $col - $b->x, 1);
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

    public function build()
    {
        $this->back = new Background($this->terminal);
        $this->frames = [
            'example' => new Example()
        ];
    }

    public function render()
    {
        $page = $this->back->getTemplate();
        foreach ($this->frames as $frame) {
            $page = $this->merge($page, $frame);
        }

        return implode("\n", $page) . "\n";
    }

    public function live()
    {
        // чтобы ожидание ввода не блокировало вывод
        stream_set_blocking(STDIN, false);
        // выключаем вывод на экран пользовательского ввода
        readline_callback_handler_install('', function () {});
        // скрываем курсор и очищаем экран
        $this->terminal->cursor('hide');
        $this->terminal->erase('screen');
        // чтобы область отрисовки всегда была в верхнем левом углу
        $this->terminal->cursor('up', $this->terminal->rows);

        while (true) {
            if ($string = fgets(STDIN)) {
                $this->keyboard->handle($string, $this->frames);
                $this->isNeedDraw = true;
            }
            if ($this->isNeedDraw) {
                $this->terminal->out($this->render());
                $this->terminal->cursor('up', $this->terminal->rows);
                $this->isNeedDraw = false;
            }
//            usleep(1000); // 1 мс
        }
    }
}