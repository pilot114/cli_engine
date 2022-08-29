<?php

namespace CliEngine\IO;

/**
 * Контекст терминала (потоки, pipes и пр.)
 */
class Terminal
{
    public bool $isCli;
    public bool $isPiped;

    public int $rows;
    public int $cols;
    public int $colors;

    // стили
    const RESET = 'reset';
    const BOLD  = 'bold';
    const DIM   = 'dim';
    const UNDER = 'under';
    const BLINK = 'blink';
    const REV   = 'rev';
    const HIDE  = 'hide';
    // цвета
    const BLACK  = 'black';
    const RED  = 'red';
    const GREEN  = 'green';
    const YELLOW  = 'yellow';
    const BLUE  = 'blue';
    const MAGENTA  = 'magenta';
    const CYAN  = 'cyan';
    const WHITE  = 'white';

    public function __construct()
    {
        $this->isCli = str_starts_with(php_sapi_name(), 'cli');
        $this->isPiped = !posix_isatty(STDOUT);

        $this->tputInfo();
    }

    public function tputInfo(): void
    {
        $this->rows = intval(`tput lines`);
        $this->cols = intval(`tput cols`);
        $this->colors = max(8, intval(`tput colors`));
    }

    /**
     * Вывод данных
     */
    public function out(string $message, bool $appendEndLine = false, $stream = STDOUT): void
    {
        fwrite($stream, $message . ($appendEndLine ? PHP_EOL : ""));
    }

    /**
     * Перемещение курсора
     */
    public function cursor(string $commandName, int $count = 1, int $row = 1, int $column = 1): void
    {
        $commands = [
            'up'    => "\033[{$count}A",
            'down'  => "\033[{$count}B",
            'right' => "\033[{$count}C",
            'left'  => "\033[{$count}D",
            'to'    => "\033[{$row};{$column}f",
            'save'  => "\0337",
            'load'  => "\0338",
            'hide'  => "\033[?25l",
            'view'  => "\033[?25h",
        ];
        if (isset($commands[$commandName])) {
            $this->out($commands[$commandName], false, STDERR);
        }
    }

    /**
     * Очистить часть экрана, относительно курсора
     */
    public function erase(string $commandName): void
    {
        $commands = [
            'screen' => "\033[2J",
            'line'   => "\033[2K",
            'up'     => "\033[1J",
            'down'   => "\033[J",
            'left'   => "\033[1K",
            'right'  => "\033[K",
        ];
        if (isset($commands[$commandName])) {
            $this->out($commands[$commandName], false, STDERR);
        }
    }

    /**
     * Установить цвет и формат
     * Поддерживается 2 формата: 8 и 256 цветов
     *
     * @param $frontColor
     * @param $backColor
     * @param $style
     */
    public function setStyle($frontColor, $backColor = null, $style = null): void
    {
        $frontColors = [
            30 => self::BLACK, self::RED, self::GREEN, self::YELLOW,
            self::BLUE, self::MAGENTA, self::CYAN, self::WHITE
        ];
        $backColors = [
            40 => self::BLACK, self::RED, self::GREEN, self::YELLOW,
            self::BLUE, self::MAGENTA, self::CYAN, self::WHITE
        ];
        $styles = [
            self::RESET, self::BOLD, self::DIM,
            4 => self::UNDER, self::BLINK,
            7 => self::REV, self::HIDE
        ];

        $attr = [];
        $attr[] = array_search($frontColor, $frontColors);
        $attr[] = array_search($backColor, $backColors);
        $attr[] = array_search($style, $styles);
        $attr = array_filter($attr);

        $attr = implode(';', $attr);
        $this->out("\033[{$attr}m", false, STDERR);
    }

    /**
     * Звоночек
     */
    public function bell(): void
    {
        $this->out("\007", false, STDERR);
    }

    public function progress($bar = false): void
    {
        $this->cursor('save');
        $i = 0;
        while($i <= 100) {
            $this->cursor('load');
            if ($bar) {
                $progressBar = sprintf("[%s]", str_repeat('#', $i) . str_repeat('.', 100 - $i));
                $this->out($progressBar, false, STDERR);
                $this->out("{$i}% complete", true);
                $this->cursor('up');
            } else {
                $this->out("{$i}% complete");
            }
            usleep(200000);
            $i+= 10;
        }
        $this->out(null, true);
    }

    /**
     * Ожидание ввода
     */
    public function wait(): void
    {
        $this->out("you sure? (y/n):", false, STDERR);
        if (strtolower(fread(STDIN, 1)) === 'y') {
            $this->out('yes!');
        } else {
            $this->out('no!!');
        }
    }

    public function handleInput(): void
    {
        $line = fgets(STDIN);
        if ($line) {
            $this->out($line);
        }
    }
}