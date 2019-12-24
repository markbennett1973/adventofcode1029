<?php

declare(strict_types = 1);

include('computer.php');

const INPUT_FILE = 'day13input.txt';

const TILE_EMPTY = 0;
const TILE_WALL = 1;
const TILE_BLOCK = 2;
const TILE_PADDLE = 3;
const TILE_BALL = 4;

$map = [];
$score = 0;

$program = loadProgram(INPUT_FILE);
runProgram($program, [], 'outputCallback', 'inputCallback');
print 'Part 1: ' . countTiles($map, TILE_BLOCK) . "\n";

// Set to free play...
$program = loadProgram(INPUT_FILE);
$program[0] = 2;
runProgram($program, [], 'outputCallback', 'inputCallback');
print "Part 2: $score\n";

function outputCallback(array $output)
{
    global $map, $score;

    if (count($output) !== 3) {
        return false;
    }

    if ($output[0] == -1 && $output[1] == 0) {
        $score = $output[2];
        //print "\nScore: $score, blocks left: " . countTiles($map, TILE_BLOCK) . "\n";
        //drawMap($map);
    } else {
        $map[$output[1]][$output[0]] = $output[2];
    }

    return true;
}

function inputCallback() {
    global $map, $score;
    $ballColumn = findColumn($map, TILE_BALL);
    $batColumn = findColumn($map, TILE_PADDLE);


    if ($ballColumn < $batColumn) {
        return -1;
    }

    if ($ballColumn > $batColumn) {
        return 1;
    }

    return 0;
}

function findColumn(array $map, int $tile): int
{
    foreach ($map as $row => $cols) {
        foreach ($cols as $col => $content) {
            if ($content === $tile) {
                return $col;
            }
        }
    }
}

function countTiles(array $map, int $tile): int
{
    $count = 0;
    foreach ($map as $row => $cols) {
        foreach ($cols as $col => $content) {
            if ($content === $tile) {
                $count++;
            }
        }
    }

    return $count;
}

function drawMap(array $map)
{
    foreach ($map as $row => $cols) {
        foreach ($cols as $col => $tile) {
            switch ($tile) {
                case TILE_WALL:
                    print '*';
                    break;

                case TILE_BLOCK:
                    print 'B';
                    break;

                case TILE_BALL:
                    print 'o';
                    break;

                case TILE_PADDLE:
                    print '-';
                    break;

                case TILE_EMPTY:
                    print ' ';
                    break;
            }
        }
        print "\n";
    }
}
