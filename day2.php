<?php

declare(strict_types = 1);

include('computer.php');

const INPUT_FILE = 'day2input.txt';

echo 'Part 1: ' . part1() . "\n";
echo 'Part 2: ' . part2() . "\n";

function part1(): string
{
    $registers = loadProgram(INPUT_FILE);
    $registers[1] = 12;
    $registers[2] = 2;
    runProgram($registers, 0);
    return (string) $registers[0];
}

function part2(): string
{
    $limit = 99;
    $target = 19690720;

    for ($noun = 0; $noun <= $limit; $noun++) {
        for ($verb = 0; $verb <= $limit; $verb++) {
            $registers = loadProgram(INPUT_FILE);
            $registers[1] = $noun;
            $registers[2] = $verb;
            runProgram($registers, 0);

            if ($registers[0] === $target) {
                return (string) (100 * $noun + $verb);
            }
        }
    }

    return '';
}