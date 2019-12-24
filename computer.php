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
const OP_ADJUST_RELATIVE_BASE = 9;
const OP_EXIT = 99;

const BY_REF = 0;
const BY_VAL = 1;
const BY_RELATIVE_REF = 2;

function loadProgram(string $filepath): array
{
    $input = file_get_contents($filepath);
    $lines = explode(',', $input);
    return array_map(function ($x): int {
        return (int) $x;
    }, $lines);
}

/**
 * @param array $registers
 * @param array $inputs
 * @return array
 * @throws Exception
 */
function runProgram(array $registers, array $inputs, $outputCallback = null, $inputCallback = null): array
{
    $stepToExecute = 0;
    $output = [];
    $relativeBase = 0;

    while ($registers[$stepToExecute] != OP_EXIT) {
        $op = (string) $registers[$stepToExecute];
        $opCode = (int) substr($op, -2);
        // print "Execute $opCode from position $stepToExecute\n";
        $params = getParams($opCode, $op, $stepToExecute, $registers, $relativeBase);

        $incrementStepCounter = true;
        switch ($opCode) {
            case OP_ADD:
                $registers[$params[2]] = $params[0] + $params[1];
                break;

            case OP_MULTIPLY:
                $registers[$params[2]] = $params[0] * $params[1];
                break;

            case OP_INPUT:
                if (is_callable($inputCallback)) {
                    $registers[$params[0]] = $inputCallback();
                } else {
                    $registers[$params[0]] = array_shift($inputs);
                }
                break;

            case OP_OUTPUT:
                $output[] = $params[0];
                if (is_callable($outputCallback)) {
                    if ($newInput = $outputCallback($output)) {
                        $inputs = $newInput;
                        $output = [];
                    }
                }
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

            case OP_ADJUST_RELATIVE_BASE:
                $relativeBase = $relativeBase + $params[0];
                break;

            default:
                die('Unexpected op code at position ' . $stepToExecute . ': ' . $registers[$stepToExecute] . "\n");
        }

        if ($incrementStepCounter) {
            $stepToExecute = $stepToExecute + count($params) + 1;
        }
    }

    // If no output instruction was executed, return the first register value instead
    if (count($output) === 0) {
        $output[] = $registers[0];
    }

    return $output;
}

/**
 * @param int $opCode
 * @param string $op
 * @param int $stepToExecute
 * @param array $registers
 * @param int $relativeBase
 * @return array
 * @throws Exception
 */
function getParams(int $opCode, string $op, int $stepToExecute, array $registers, int $relativeBase): array
{
    $parameterCount = getParameterCount($opCode);
    $parameterModes = getParameterModes($op, $parameterCount);
    $params = [];
    for ($i = 0; $i < $parameterCount; $i++) {
        $registerIndex = $stepToExecute + $i + 1;
        // The last parameter is always a position, but it may be a relative position
        if (isWriteParameter($opCode, $i)) {
            $params[$i] = getRegisterValue($registers, $registerIndex);
            if ($parameterModes[$i] === BY_RELATIVE_REF) {
                $params[$i] += $relativeBase;
            }
        } elseif ($parameterModes[$i] === BY_VAL) {
            $params[$i] = getRegisterValue($registers, $registerIndex);
        } else {
            $paramPos = getRegisterValue($registers, $registerIndex);

            if ($parameterModes[$i] === BY_RELATIVE_REF) {
                $paramPos = $paramPos + $relativeBase;
            }
            $params[$i] = getRegisterValue($registers, $paramPos);
        }
    }

    return $params;
}

function getRegisterValue(array $registers, int $position): int
{
    if (array_key_exists($position, $registers)) {
        return $registers[$position];
    }

    return 0;
}

function isWriteParameter(int $opCode, int $paramPosition): bool
{
    switch ($opCode) {
        case OP_ADD:
        case OP_MULTIPLY:
        case OP_LESS_THAN:
        case OP_EQUALS:
            return $paramPosition === 2;

        case OP_INPUT:
            return $paramPosition === 0;

        default:
            return false;
    }
}

/**
 * @param int $opCode
 * @return int
 * @throws Exception
 */
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
        case OP_ADJUST_RELATIVE_BASE:
            return 1;

        default:
            throw new Exception('Unexpected op code ' . $opCode);
    }
}

/**
 * @param string $op
 * @param int $parameterCount
 * @return array
 * @throws Exception
 */
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
            switch ($modeChar) {
                case "0":
                    $mode = BY_REF;
                    break;
                case "1":
                    $mode = BY_VAL;
                    break;
                case "2":
                    $mode = BY_RELATIVE_REF;
                    break;
                default:
                    throw new Exception("Uknown mode character: $modeChar");
            }
        } else {
            $mode = BY_REF;
        }

        $modes[$i] = $mode;
    }

    return $modes;
}
