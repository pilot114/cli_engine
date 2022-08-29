<?php

namespace CliEngine\Draw;

use CliEngine\Draw\Frames\Background;
use CliEngine\Draw\Frames\IFrame;
use CliEngine\IO\Keyboard;
use CliEngine\IO\Terminal;
use CliEngine\Draw\Frames\Example;

/**
 * Отвечает за контроль отрисовки
 */
class Canvas
{
    protected Terminal $terminal;
    protected Keyboard $keyboard;
    protected Unicode $unicode;
    protected bool $isNeedDraw = true;

    // участки для отрисовки
    protected Background $back;
    protected array $frames = [];

    public function __construct(Terminal $t)
    {
        $this->unicode = new Unicode();
        $this->keyboard = new Keyboard();
        $this->terminal = $t;
    }

    public function merge(array $base, IFrame $frame): array
    {
        $mask = $frame->getTemplate();

        $result = [];
        $row = 0;
        while ($row < $this->terminal->rows) {
            $col = 0;
            $line = '';
            while ($col < $this->terminal->cols) {
                if (
                    $row >= $frame->y && $row+1 <= ($frame->y + $frame->getHeight())
                    &&
                    $col >= $frame->x && $col+1 <= ($frame->x + $frame->getWidth())
                ) {
                    $line .= mb_substr($mask[$row - $frame->y], $col - $frame->x, 1);
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

    public function build(): void
    {
        $this->back = new Background($this->terminal);
        $this->frames = [
            'example' => new Example()
        ];
    }

    public function render(): string
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
        readline_callback_handler_install('', fn() => null);
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
            usleep(0);
        }
    }
}