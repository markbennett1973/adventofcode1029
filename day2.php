<?php

declare(strict_types = 1);

const INPUT_FILE = 'day2input.txt';
const OP_ADD = 1;
const OP_MULTIPLY = 2;
const OP_EXIT = 99;

echo 'Part 1: ' . part1() . "\n";
echo 'Part 2: ' . part2() . "\n";

function part1(): string
{
    $registers = initRegisters();
    $registers[1] = 12;
    $registers[2] = 2;
    runProgram($registers);
    return (string) $registers[0];
}

function part2(): string
{
    $limit = 99;
    $target = 19690720;

    for ($noun = 0; $noun <= $limit; $noun++) {
        for ($verb = 0; $verb <= $limit; $verb++) {
            $registers = initRegisters();
            $registers[1] = $noun;
            $registers[2] = $verb;
            runProgram($registers);

            if ($registers[0] === $target) {
                return (string) (100 * $noun + $verb);
            }
        }
    }

    return '';
}

function initRegisters(): array
{
    $input = file_get_contents(INPUT_FILE);
    return explode(',', $input);
}

function runProgram(array &$registers)
{
    $stepToExecute = 0;

    while ($registers[$stepToExecute] != OP_EXIT) {
        $opCode = $registers[$stepToExecute];
        $input1Pos = $registers[$stepToExecute + 1];
        $input2Pos = $registers[$stepToExecute + 2];
        $outputPos = $registers[$stepToExecute + 3];

        switch ($opCode) {
            case OP_ADD:
                $registers[$outputPos] = $registers[$input1Pos] + $registers[$input2Pos];
                break;

            case OP_MULTIPLY:
                $registers[$outputPos] = $registers[$input1Pos] * $registers[$input2Pos];
                break;

            default:
                die('Unexpected op code at position ' . $stepToExecute . ': ' . $registers[$stepToExecute] . "\n");
        }

        $stepToExecute = $stepToExecute + 4;
    }

    return $registers;
}
