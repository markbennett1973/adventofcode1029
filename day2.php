<?php

declare(strict_types = 1);

include('computer.php');

const INPUT_FILE = 'day2input.txt';

echo 'Part 1: ' . part1() . "\n";
echo 'Part 2: ' . part2() . "\n";

function part1(): int
{
    $registers = loadProgram(INPUT_FILE);
    $registers[1] = 12;
    $registers[2] = 2;
    return runProgram($registers, []);
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
            $output = runProgram($registers, []);

            if ($output === $target) {
                return (string) (100 * $noun + $verb);
            }
        }
    }

    return '';
}