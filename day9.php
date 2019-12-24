<?php

declare(strict_types = 1);

include('computer.php');

const INPUT_FILE = 'day9input.txt';

echo 'Part 1: ' . part1() . "\n";
echo 'Part 2: ' . part2() . "\n";

function part1(): int
{
    $program = loadProgram(INPUT_FILE);
    $output = runProgram($program, [1]);
    return $output[0];
}

function part2(): int
{
    $program = loadProgram(INPUT_FILE);
    $output = runProgram($program, [2]);
    return $output[0];
}

function getInput(): array
{
    $input = file_get_contents(INPUT_FILE);
    return explode("\n", $input);
}
