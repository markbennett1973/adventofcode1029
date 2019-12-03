<?php

declare(strict_types = 1);

const INPUT_FILE = 'day3input.txt';

$wires = getInput();
$grid1 = addWireToGrid($wires[0]);
$grid2 = addWireToGrid($wires[1]);

echo 'Part 1: ' . getDistanceToClosestIntersection($grid1, $grid2, false) . "\n";
echo 'Part 2: ' . getDistanceToClosestIntersection($grid1, $grid2, true) . "\n";

function getInput(): array
{
    $wires = [];
    $input = file_get_contents(INPUT_FILE);
    $lines = explode("\n", $input);
    foreach ($lines as $line) {
        $wires[] = explode(',', $line);
    }

    return $wires;
}

function addWireToGrid(array $wire)
{
    $grid = [];
    $currentRow = $currentCol = 0;
    $totalDistance = 1;
    foreach ($wire as $step) {
        $direction = substr($step, 0, 1);
        $stepDistance = substr($step, 1);

        for ($i = 0; $i < $stepDistance; $i++) {
            switch ($direction) {
                case 'L':
                    $currentCol--;
                    break;

                case 'R':
                    $currentCol++;
                    break;

                case 'U':
                    $currentRow--;
                    break;

                case 'D':
                    $currentRow++;
                    break;
            }

            if (!array_key_exists($currentRow, $grid)) {
                $grid[$currentRow] = [];
            }

            if (!array_key_exists($currentCol, $grid[$currentRow])) {
                $grid[$currentRow][$currentCol] = $totalDistance;
            }

            $totalDistance++;
        }
    }

    return $grid;
}

function getDistanceToClosestIntersection(array $grid1, array $grid2, bool $getTotalDistance): int
{
    $minDistance = null;
    foreach ($grid1 as $row => $columns) {
        foreach ($columns as $column => $dummy) {
            if (array_key_exists($row, $grid2) && array_key_exists($column, $grid2[$row])) {
                if ($getTotalDistance) {
                    $distance = $grid1[$row][$column] + $grid2[$row][$column];
                } else {
                    $distance = abs($row) + abs($column);
                }

                if ($minDistance === null || $distance < $minDistance) {
                    $minDistance = $distance;
                }
            }
        }
    }

    return $minDistance;
}