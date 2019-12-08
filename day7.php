<?php

declare(strict_types = 1);

include('computer.php');

const INPUT_FILE = 'day7input.txt';

/**
 * Code passes all tests, but actual problem input errors. Retry when bug from day 5 is fixed.
 */

echo 'Part 1: ' . part1() . "\n";
//echo 'Part 2: ' . part2() . "\n";

function part1(): int
{
    $program = loadProgram(INPUT_FILE);
    $maxOutput = 0;
    foreach (getAllPhaseCombinations() as $phases) {
        $output = getFinalStageOutput($program, $phases);
        $maxOutput = $output > $maxOutput ? $output : $maxOutput;
    }

    return $maxOutput;
}

function part2(): int
{
    $program = loadProgram(INPUT_FILE);
    $input = 5;
    return runProgram($program, $input);
}

function getFinalStageOutput(array $program, array $phases): int
{
    $previousStageOutput = 0;

    for ($i = 0; $i < 5; $i++) {
        $previousStageOutput = runProgram($program, [$phases[$i], $previousStageOutput]);
    }

    return $previousStageOutput;
}

function getAllPhaseCombinations(): array
{
    $phases = [0,1,2,3,4];

    $size = count($phases) - 1;
    $perm = range(0, $size);
    $j = 0;

    do {
        foreach ($perm as $i) {
            $perms[$j][] = $phases[$i];
        }
    } while ($perm = pc_next_permutation($perm, $size) and ++$j);

    return $perms;
}

/**
 * @param $p
 * @param $size
 * @return bool
 * @see https://docstore.mik.ua/orelly/webprog/pcook/ch04_26.htm
 */
function pc_next_permutation($p, $size) {
    // slide down the array looking for where we're smaller than the next guy
    for ($i = $size - 1; $p[$i] >= $p[$i+1]; --$i) { }

    // if this doesn't occur, we've finished our permutations
    // // the array is reversed: (1, 2, 3, 4) => (4, 3, 2, 1)
    if ($i == -1) { return false; }

    // slide down the array looking for a bigger number than what we found before
    for ($j = $size; $p[$j] <= $p[$i]; --$j) { }

    // swap them
    $tmp = $p[$i]; $p[$i] = $p[$j]; $p[$j] = $tmp;

    // now reverse the elements in between by swapping the ends
    for (++$i, $j = $size; $i < $j; ++$i, --$j) {
        $tmp = $p[$i];
        $p[$i] = $p[$j];
        $p[$j] = $tmp;
    }

    return $p;
}


