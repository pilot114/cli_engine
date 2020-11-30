<?php

namespace CliEngine\Draw;

/**
 * Отрисовка линий
 */
class Unicode
{
    protected $diaps = [
        ['0020', '007E', 'Основная латиница'],
        ['00A0', '00FF', 'Дополнение к латинице — 1'],
        ['0250', '02AF', 'Международный фонетический алфавит'],
        ['0400', '04FF', 'Кириллица'],
        ['16A0', '16FF', 'Руны'],
        ['2070', '209C', 'Надстрочные и подстрочные знаки'],
        ['20A0', '20CF', 'Знаки валют'],
        ['2190', '21FF', 'Стрелки'],
        ['2200', '22FF', 'Математические операторы'],
        ['2300', '23FF', 'Разные технические знаки'],
        ['2460', '24FF', 'Обрамлённые буквы и цифры'],
        ['2500', '257F', 'Псевдографика'],
        ['2580', '259F', 'Блочные элементы'],
        ['25A0', '25FF', 'Геометрические фигуры'],
        ['2600', '26FF', 'Разные символы'],
        ['2700', '27BF', 'Dingbats'],
        ['27F0', '27FF', 'Дополнительные стрелки — A'],
        ['2900', '297F', 'Дополнительные стрелки — B'],
        ['1F800', '1F8FF', 'Дополнительные стрелки — C'],
        ['2800', '28FF', 'Шрифт Брайля'],
        ['2C00', '2C5F', 'Глаголица'],
        ['1F000', '1F02F', 'Кости для маджонга'],
        ['1F030', '1F09F', 'Кости для домино'],
        ['1F0A0', '1F0FF', 'Игральные карты'],
        ['1FA00', '1FA6F', 'Шахматные символы'],
        ['1F300', '1F5FF', 'Разные символы и пиктограммы'],
        ['1F600', '1F64F', 'Эмотиконы'],
        ['1F680', '1F6FF', 'Транспортные и картографические символы'],
        ['1F700', '1F77F', 'Алхимические символы'],
        ['1F780', '1F7FF', 'Расширенные геометрические фигуры'],
        ['1F900', '1F9FF', 'Дополнительные символы и пиктограммы'],
        ['1FA70', '1FAFF', 'Расширенные символы и пиктограммы — A'],
    ];

    public function getDiap($name)
    {
        $diap = array_filter($this->diaps, function($item) use ($name) {
            return $item[2] === $name;
        });
        $diap = $diap[array_key_first($diap)];

        $pos = hexdec($diap[0]);
        $end = hexdec($diap[1]);

        $symbols = [];
        while ($pos <= $end) {
            $pos++;
            $symbols[] = $this->unichr($pos);
        }
        return $symbols;
    }

    protected function unichr($i)
    {
        return @iconv('UCS-4LE', 'UTF-8', pack('V', $i));
    }

    public function printAll()
    {
        foreach ($this->diaps as $diap) {
            echo "$diap[2]\n";
            $symbols = $this->getDiap($diap[2]);
            foreach ($symbols as $symbol) {
                echo sprintf("%s: %s", $symbol, strlen($symbol));
            }
            echo "\n";
        }
    }
}