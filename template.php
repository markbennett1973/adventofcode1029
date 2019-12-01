<?php

declare(strict_types = 1);

const INPUT_FILE = 'day1input.txt';

echo 'Part 1: ' . part1() . "\n";
echo 'Part 2: ' . part2() . "\n";

function part1(): string
{
    return '';
}

function part2(): string
{
    return '';
}

function getInput(): array
{
    $input = file_get_contents(INPUT_FILE);
    return explode("\n", $input);
}