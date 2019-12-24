<?php

declare(strict_types = 1);

include('computer.php');

const INPUT_FILE = 'day11input.txt';

const BLACK = 0;
const WHITE = 1;

$program = loadProgram(INPUT_FILE);
$robot = new Robot();

$input = $robot->getCurrentPositionColour();
runProgram($program, [$input], function ($output) use ($robot) {
    if (count($output) !== 2) {
        return null;
    }

    // Paint the current position with colour $output[0]
    $robot->paint($output[0]);
    $robot->rotate($output[1]);
    $robot->move();

    $newPositionColour = $robot->getCurrentPositionColour();
    return [$newPositionColour];
});

print "Part 1: " . countPaintedPanels($robot->getMap()) . "\n";
printMap($robot->getMap());

function countPaintedPanels(array $map): int
{
    $panels = 0;
    foreach ($map as $row => $cols) {
        foreach ($cols as $col => $colour) {
            $panels++;
        }
    }

    return $panels;
}

function printMap(array $map)
{
    $extents = getMapExtents($map);
    for ($row = $extents[0]; $row <= $extents[1]; $row++) {
        for ($col = $extents[2]; $col <= $extents[3]; $col++) {
            if (array_key_exists($row, $map)
                && array_key_exists($col, $map[$row])
                && $map[$row][$col] === WHITE) {
                print '**';
            } else {
                print '  ';
            }
        }

        print "\n";
    }
}

function getMapExtents(array $map): array
{
    $minRow = $minCol = PHP_INT_MAX;
    $maxRow = $maxCol = PHP_INT_MIN;

    foreach ($map as $row => $cols) {
        if ($row < $minRow) {
            $minRow = $row;
        }

        if ($row > $maxRow) {
            $maxRow = $row;
        }

        foreach ($cols as $col => $colour) {
            if ($col < $minCol) {
                $minCol = $col;
            }

            if ($col > $maxCol) {
                $maxCol = $col;
            }
        }
    }

    return [
        $minRow,
        $maxRow,
        $minCol,
        $maxCol
    ];
}

class Robot
{
    private int $row;
    private int $col;
    private int $direction;
    private array $map;

    const DIR_UP = 0;
    const DIR_RIGHT = 1;
    const DIR_DOWN = 2;
    const DIR_LEFT = 3;

    const ROTATE_LEFT = 0;
    const ROTATE_RIGHT = 1;

    public function __construct()
    {
        $this->row = 0;
        $this->col = 0;
        $this->direction = self::DIR_UP;
        $this->map = [];
        $this->map[0][0] = WHITE;
    }

    public function getPosition(): array
    {
        return [$this->row, $this->col];
    }

    public function rotate(int $direction)
    {
        if ($direction === self::ROTATE_LEFT) {
            $this->direction--;
        }

        if ($direction === self::ROTATE_RIGHT) {
            $this->direction++;
        }

        if ($this->direction < 0) {
            $this->direction += 4;
        }

        if ($this->direction > 3) {
            $this->direction -= 4;
        }
    }

    public function move()
    {
        switch ($this->direction) {
            case self::DIR_UP:
                $this->row--;
                break;

            case self::DIR_RIGHT:
                $this->col++;
                break;

            case self::DIR_DOWN:
                $this->row++;
                break;

            case self::DIR_LEFT:
                $this->col--;
                break;
        }
    }

    public function getCurrentPositionColour(): int
    {
        if (array_key_exists($this->row, $this->map)) {
            if (array_key_exists($this->col, $this->map[$this->row])) {
                return $this->map[$this->row][$this->col];
            }
        }

        return BLACK;
    }

    public function paint(int $colour)
    {
        $this->map[$this->row][$this->col] = $colour;
    }

    public function getMap(): array
    {
        return $this->map;
    }
}
