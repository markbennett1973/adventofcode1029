<?php

declare(strict_types = 1);

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

function loadProgram(string $filepath): array
{
    $input = file_get_contents($filepath);
    $lines = explode(',', $input);
    return array_map(function ($x): int {
        return (int) $x;
    }, $lines);
}

function runProgram(array $registers, array $inputs): int
{
    $stepToExecute = 0;
    $output = 0;
    $outputSet = false;

    while ($registers[$stepToExecute] != OP_EXIT) {
        $op = (string) $registers[$stepToExecute];
        $opCode = (int) substr($op, -2);
        //print "Execute $opCode from position $stepToExecute\n";
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
                $registers[$params[0]] = array_shift($inputs);
                break;

            case OP_OUTPUT:
                $output = $registers[$params[0]];
                $outputSet = true;
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

    // If no output instruction was executed, return the first register value instead
    return $outputSet ? $output : $registers[0];
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
