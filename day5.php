<?php

declare(strict_types = 1);

include('computer.php');

const INPUT_FILE = 'day5input.txt';

//runTests();

echo 'Part 1: ' . part1() . "\n";
echo 'Part 2: ' . part2() . "\n";

function part1(): int
{
    $program = loadProgram(INPUT_FILE);
    $input = 1;
    return runProgram($program, [$input]);
}

// part 1 5182797

function part2(): int
{
    $program = loadProgram(INPUT_FILE);
    $input = 5;
    return runProgram($program, [$input]);
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
            print "$input gives " . runProgram($testPrograms[$i], [$input]) . "\n";
        }
    }

    for ($i = 4; $i <= 5; $i++) {
        print "Testing program " . ($i + 1) . ":\n";
        for ($input = -3; $input <= 3; $input++) {
            print "$input gives " . runProgram($testPrograms[$i], [$input]) . "\n";
        }
    }

    for ($input = 6; $input <= 10; $input++) {
        print "$input gives " . runProgram($testPrograms[6], [$input]) . "\n";
    }
}
