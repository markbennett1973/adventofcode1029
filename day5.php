<?php

declare(strict_types = 1);

const INPUT_FILE = 'day5input.txt';
const OP_ADD = 1;
const OP_MULTIPLY = 2;
const OP_INPUT = 3;
const OP_OUTPUT = 4;
const OP_JUMP_IF_TRUE = 5;
const OP_JUMP_IF_FALSE = 6;
const OP_LESS_THAN = 7;
const OP_EQUALS = 8;
const OP_EXIT = 99;

const BY_REF = 0;
const BY_VAL = 1;

// runTests();

echo 'Part 1: ' . part1() . "\n";
echo 'Part 2: ' . part2() . "\n";

function part1(): int
{
    $registers = initRegisters();
    $input = 1;
    return runProgram($registers, $input);
}

// part 1 5182797

function part2(): int
{
    $registers = initRegisters();
    $input = 5;
    return runProgram($registers, $input);
}

function runTests()
{
    $testPrograms = [
        [3,9,8,9,10,9,4,9,99,-1,8], // pass
        [3,9,7,9,10,9,4,9,99,-1,8], // pass
        [3,3,1108,-1,8,3,4,3,99], // pass
        [3,3,1107,-1,8,3,4,3,99], // pass
        [3,12,6,12,15,1,13,14,13,4,13,99,-1,0,1,9], // fails on input = 0
        [3,3,1105,-1,9,1101,0,0,12,4,12,99,1], // pass
        [3,21,1008,21,8,20,1005,20,22,107,8,21,20,1006,20,31,1106,0,36,98,0,0,1002,21,125,20,4,20,1105,1,46,104,999,1105,1,46,1101,1000,1,20,4,20,1105,1,46,98,99], // fails
    ];

    for ($i = 0; $i <= 3; $i++) {
        print "Testing program " . ($i + 1) . ":\n";
        for ($input = 6; $input <= 10; $input++) {
            print "$input gives " . runProgram($testPrograms[$i], $input) . "\n";
        }
    }

    for ($i = 4; $i <= 5; $i++) {
        print "Testing program " . ($i + 1) . ":\n";
        for ($input = -3; $input <= 3; $input++) {
            print "$input gives " . runProgram($testPrograms[$i], $input) . "\n";
        }
    }

    for ($input = 6; $input <= 10; $input++) {
        print "$input gives " . runProgram($testPrograms[6], $input) . "\n";
    }
}

function initRegisters(): array
{
    $input = file_get_contents(INPUT_FILE);
    $lines = explode(',', $input);
    return array_map(function ($x): int {
        return (int) $x;
    }, $lines);
}

function runProgram(array $registers, int $input): int
{
    $stepToExecute = 0;
    $output = 0;

    while ($registers[$stepToExecute] != OP_EXIT) {
        $op = (string) $registers[$stepToExecute];
        $opCode = (int) substr($op, -2);
        // print "Execute $opCode from position $stepToExecute\n";
        $params = getParams($opCode, $op, $stepToExecute, $registers);


        $incrementStepCounter = true;
        switch ($opCode) {
            case OP_ADD:
                $registers[$params[2]] = $params[0] + $params[1];
                break;

            case OP_MULTIPLY:
                $registers[$params[2]] = $params[0] * $params[1];
                break;

            case OP_INPUT:
                $registers[$params[0]] = $input;
                break;

            case OP_OUTPUT:
                $output = $registers[$params[0]];
                break;

            case OP_JUMP_IF_TRUE:
                if ($params[0] !== 0) {
                    $stepToExecute = $params[1];
                    $incrementStepCounter = false;
                }
                break;

            case OP_JUMP_IF_FALSE:
                if ($params[0] === 0) {
                    $stepToExecute = $params[1];
                    $incrementStepCounter = false;
                }
                break;

            case OP_LESS_THAN:
                $registers[$params[2]] = ($params[0] < $params[1]) ? 1 : 0;
                break;

            case OP_EQUALS:
                $registers[$params[2]] = ($params[0] === $params[1]) ? 1 : 0;
                break;

            default:
                die('Unexpected op code at position ' . $stepToExecute . ': ' . $registers[$stepToExecute] . "\n");
        }

        if ($incrementStepCounter) {
            $stepToExecute = $stepToExecute + count($params) + 1;
        }
    }

    return $output;
}

function getParams(int $opCode, string $op, int $stepToExecute, array $registers): array
{
    $parameterCount = getParameterCount($opCode);
    $parameterModes = getParameterModes($op, $parameterCount);
    $params = [];
    for ($i = 0; $i < $parameterCount; $i++) {
        $registerIndex = $stepToExecute + $i + 1;
        // The last parameter is always a position
        if ($i === $parameterCount - 1) {
            $params[$i] = $registers[$registerIndex];
        } elseif ($parameterModes[$i] === BY_VAL) {
            $params[$i] = $registers[$registerIndex];
        } else {
            $paramPos = $registers[$registerIndex];
            $params[$i] = $registers[$paramPos];
        }
    }

    return $params;
}

function getParameterCount(int $opCode): int
{
    switch ($opCode) {
        case OP_ADD:
        case OP_MULTIPLY:
        case OP_LESS_THAN:
        case OP_EQUALS:
            return 3;

        case OP_JUMP_IF_TRUE:
        case OP_JUMP_IF_FALSE:
            return 2;

        case OP_INPUT:
        case OP_OUTPUT:
            return 1;

        default:
            throw new Exception('Unexpected op code ' . $opCode);
    }
}

function getParameterModes(string $op, int $parameterCount): array
{
    $modes = [];
    // Remove the op code, to just leave the parameter modes
    $op = substr($op, 0, strlen($op) - 2);
    $len = strlen($op);
    for ($i = 0; $i < $parameterCount; $i++) {
        $pos = $len - $i - 1;
        if ($pos >= 0) {
            $modeChar = substr($op, $pos, 1);
            $mode = $modeChar === "0" ? BY_REF : BY_VAL;
        } else {
            $mode = BY_REF;
        }

        $modes[$i] = $mode;
    }

    return $modes;
}
