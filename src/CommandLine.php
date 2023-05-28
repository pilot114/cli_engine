<?php

namespace Snidget;

use Snidget\Enum\CLIColor;
use Snidget\Enum\CLIStyle;

/**
 * TODO Features:
 * - commands and subcommands by annotations
 * - options and arguments by types with annotations
 * - color output
 * - interactive input
 * - show progress
 * - cursor manipulate
 */
class CommandLine
{
    protected bool $isPiped = false;
    protected int $rows;
    protected int $cols;
    protected int $colors;

    public function __construct()
    {
        // https://stackoverflow.com/a/11327451
        $fstat = fstat(STDOUT);
        if ($fstat) {
            $this->isPiped = ($fstat['mode'] & 0170000) === 0010000;
        }
        $this->rows = intval(`tput lines`);
        $this->cols = intval(`tput cols`);
        $this->colors = max(8, intval(`tput colors`));
    }

    /**
     * Вывод данных
     */
    public function out(string $message, bool $isEndLine = false, bool $isError = false): void
    {
        fwrite($isError ? STDERR : STDOUT, $message . ($isEndLine ? PHP_EOL : ""));
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
            $this->out($commands[$commandName], false, true);
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
            $this->out($commands[$commandName]);
        }
    }

    /**
     * Установить цвет и формат
     */
    public function setStyle(string $frontColor, ?string $backColor = null, ?string $style = null): void
    {
        $frontColors = [
            30 => CLIColor::BLACK,
            31 => CLIColor::RED,
            32 => CLIColor::GREEN,
            33 => CLIColor::YELLOW,
            34 => CLIColor::BLUE,
            35 => CLIColor::MAGENTA,
            36 => CLIColor::CYAN,
            37 => CLIColor::WHITE
        ];
        $backColors = [
            40 => CLIColor::BLACK,
            41 => CLIColor::RED,
            42 => CLIColor::GREEN,
            43 => CLIColor::YELLOW,
            44 => CLIColor::BLUE,
            45 => CLIColor::MAGENTA,
            46 => CLIColor::CYAN,
            47 => CLIColor::WHITE
        ];
        $styles = [
            0 => CLIStyle::RESET,
            1 => CLIStyle::BOLD,
            2 => CLIStyle::DIM,
            4 => CLIStyle::UNDER,
            5 => CLIStyle::BLINK,
            7 => CLIStyle::REV,
            8 => CLIStyle::HIDE
        ];

        $attr = [
            array_search($frontColor, $frontColors),
            array_search($backColor, $backColors),
            array_search($style, $styles),
        ];
        $attr = array_filter($attr);

        $attr = implode(';', $attr);
        $this->out("\033[{$attr}m", false, true);
    }

    /**
     * Звоночек
     */
    public function bell(): void
    {
        $this->out("\007", false, true);
    }

    public function progress(bool $bar = false): void
    {
        $this->cursor('save');
        $i = 0;
        while ($i <= 100) {
            $this->cursor('load');
            if ($bar) {
                $progressBar = sprintf("[%s]", str_repeat('#', $i) . str_repeat('.', 100 - $i));
                $this->out($progressBar, false, true);
                $this->out("{$i}% complete", true);
                $this->cursor('up');
            } else {
                $this->out("{$i}% complete");
            }
            usleep(200000);
            $i += 10;
        }
        $this->out('', true);
    }

    /**
     * Ожидание ввода
     */
    public function wait(): void
    {
        $this->out("you sure? (y/n):", false, true);
        if (strtolower(fread(STDIN, 1) ?: '') === 'y') {
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
