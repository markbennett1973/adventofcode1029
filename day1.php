<?php

declare(strict_types = 1);

const INPUT_FILE = 'day1input.txt';

echo 'Part 1: ' . part1() . "\n";
echo 'Part 2: ' . part2() . "\n";

function part1(): int
{
    $fuel = 0;
    foreach (getInput() as $mass) {
        $fuel += getFuelForMass((int) $mass);
    }

    return $fuel;
}

function part2(): int
{
    $fuel = 0;
    foreach (getInput() as $mass) {
        $fuel += getTotalFuelForMass((int) $mass);

    }

    return $fuel;
}

function getInput(): array
{
    $input = file_get_contents(INPUT_FILE);
    return explode("\n", $input);
}

function getFuelForMass(int $mass): int
{
    $fuel = floor($mass / 3) - 2;
    if ($fuel < 0) {
        return 0;
    }

    return (int) $fuel;
}

function getTotalFuelForMass(int $mass): int
{
    $incrementalFuel = getFuelForMass($mass);
    $totalFuel = $incrementalFuel;
    while ($incrementalFuel = getFuelForMass($incrementalFuel)) {
        $totalFuel += $incrementalFuel;
    }

    return $totalFuel;
}