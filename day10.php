<?php

declare(strict_types = 1);

const INPUT_FILE = 'day10input.txt';

echo 'Part 1: ' . part1() . "\n";
echo 'Part 2: ' . part2() . "\n";

function part1(): int
{
    $map = getMap();
    return getMaxViews($map);
}

function part2(): string
{
    return '';
}

function getMap(): array
{
    $map = [];
    $input = file_get_contents(INPUT_FILE);
    foreach (explode("\n", $input) as $row => $rowContent) {
        $cols = strlen($rowContent);
        for ($col = 0; $col < $cols; $col++) {
            $map[$row][$col] = $rowContent[$col] === '#' ? 1 : 0;
        }
    }

    return $map;
}

function getMaxViews(array $map): int
{
    $maxViews = 0;

    foreach ($map as $row => $cols) {
        foreach ($cols as $col => $hasAsteroid) {
            if ($hasAsteroid) {
                $views = countVisibleFromPoint($map, $row, $col);
                $maxViews = $views > $maxViews ? $views : $maxViews;
            }
        }
    }

    return $maxViews;
}

function countVisibleFromPoint(array $map, int $pointRow, int $pointCol): int
{
    $visible = 0;
    foreach ($map as $row => $colData) {
        foreach ($colData as $col => $hasAsteroid) {
            if ($hasAsteroid) {
                if (isAsteroidVisibleFromPoint($map, $row, $col, $pointRow, $pointCol)) {
                    $visible++;
                }
            }
        }
    }

    return $visible;
}

function isAsteroidVisibleFromPoint(array $map, $row, $col, $pointRow, $pointCol): bool
{
    // A point is not visible from itself
    if ($row === $pointRow && $col === $pointCol) {
        return false;
    }

    // If any intermediate cells contain an asteroid, then the destination is not visible
    $intermediateCells = calculateIntermediateCells($row, $col, $pointRow, $pointCol);
    foreach ($intermediateCells as $cell) {
        $cellRow = $cell[0];
        $cellCol = $cell[1];
        if ($map[$cellRow][$cellCol] === 1) {
            return false;
        }
    }

    return true;
}

/**
 * Calculate cells which lie exactly between two points
 * @param int $startRow
 * @param int $startCol
 * @param int $endRow
 * @param int $endCol
 * @return array
 *   0 => [row, col],
 *   1 => [row, col],
 *   2 => [row, col],
 *   ...
 */
function calculateIntermediateCells(int $startRow, int $startCol, int $endRow, int $endCol): array
{
    $intermediateCells = [];

    // Always check in a positive gradient direction - makes the maths a bit simpler. So if $end < $start, swap them.
    if ($endRow < $startRow) {
        $tmp = $endRow;
        $endRow = $startRow;
        $startRow = $tmp;
    }

    if ($endCol < $startCol) {
        $tmp = $endCol;
        $endCol = $startCol;
        $startCol = $tmp;
    }

    $dRow = $endRow - $startRow;
    $dCol = $endCol - $startCol;

    // Special case for vertical lines - we can't calculate a gradient
    if ($dCol === 0) {
        return calculateVerticalIntermediateCells($startRow, $startCol, $endRow, $endCol);
    }

    $gradient = $dRow / $dCol;
    // don't include our start and end points
    for ($col = $startCol + 1; $col < $endCol; $col++) {
        $row = $startRow + ($gradient * ($col - $startCol));
        // Check if we got an exact integer (accounting for float rounding)
        if (abs($row - round($row)) < 0.0001) {
            $intermediateCells[] = [(int) $row, $col];
        }
    }

    return $intermediateCells;
}


function calculateVerticalIntermediateCells(int $startRow, int $startCol, int $endRow, int $endCol): array
{
    $intermediateCells = [];
    // don't include the start and end points
    for ($row = $startRow + 1; $row < $endRow; $row++) {
        $intermediateCells[] = [$row, $startCol];
    }

    return $intermediateCells;
}
