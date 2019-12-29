<?php

declare(strict_types = 1);

include('computer.php');

const INPUT_FILE = 'day15input.txt';

const MOVE_NORTH = 1;
const MOVE_SOUTH = 2;
const MOVE_WEST = 3;
const MOVE_EAST = 4;

const STATUS_WALL = 0;
const STATUS_MOVED = 1;
const STATUS_FOUND = 2;

// The map is complete when we only have 23 unknown spaces left
const UNKNOWN_SPACES = 23;


$map[0][0] = ' ';
$currentPosition = new Coord(0, 0);
$lastMovement = MOVE_NORTH;
$foundPosition = new Coord(0, 0);
$originPosition = new Coord(0, 0);

// Run generateMap first, to generate the map. Save map to day15map.txt
//generateMap();
print 'Part 1: ' . part1() . "\n";
print 'Part 2: ' . part2() . "\n";

function generateMap()
{
    $program = loadProgram(INPUT_FILE);
    runProgram($program, [MOVE_NORTH], 'outputCallback');
}

function part1(): int
{
    readMap();
    return findShortestPath();
}

function part2(): int
{
    readMap();
    return floodOxygen();
}

function readMap()
{
    global $map, $originPosition, $foundPosition;

    $rows = explode("\n", file_get_contents('day15map.txt'));
    foreach ($rows as $rowIndex => $row) {
        for($col = 0; $col < strlen($row); $col++) {
            $map[$col][$rowIndex] = $row[$col];

            if ($map[$col][$rowIndex] === 'O') {
                $originPosition->x = $col;
                $originPosition->y = $rowIndex;
                $map[$col][$rowIndex] = ' ';
            }

            if ($map[$col][$rowIndex] === 'T') {
                $foundPosition->x = $col;
                $foundPosition->y = $rowIndex;
                $map[$col][$rowIndex] = ' ';
            }
        }
    }
}

function outputCallback(array $output): array
{
    global $lastMovement, $foundPosition;
    static $steps = 0;

    updateMapAndPosition($output[0]);
    $lastMovement = chooseNextDirection();

    $steps++;
    if ($steps % 10000 === 0) {
        print "\nMap after $steps steps:\n";
        $unknownSpaces = printMap();
        if ($unknownSpaces === UNKNOWN_SPACES) {
            print "Finished building map\n";
            exit;
        }
    }

    return [$lastMovement];
}

function updateMapAndPosition(int $output)
{
    global $map, $currentPosition, $lastMovement, $foundPosition;

    switch ($lastMovement) {
        case MOVE_NORTH:
            $newX = $currentPosition->x;
            $newY = $currentPosition->y - 1;
            break;

        case MOVE_EAST:
            $newX = $currentPosition->x + 1;
            $newY = $currentPosition->y;
            break;

        case MOVE_SOUTH:
            $newX = $currentPosition->x;
            $newY = $currentPosition->y + 1;
            break;

        case MOVE_WEST:
            $newX = $currentPosition->x - 1;
            $newY = $currentPosition->y;
            break;
    }

    // Update map depending on output, and update position if we moved
    switch ($output) {
        case STATUS_WALL:
            $map[$newX][$newY] = '#';
            break;

        case STATUS_MOVED:
        case STATUS_FOUND:
            $map[$newX][$newY] = ' ';
            $currentPosition->x = $newX;
            $currentPosition->y = $newY;
            break;
    }

    if ($output === STATUS_FOUND) {
        $foundPosition->x = $currentPosition->x;
        $foundPosition->y = $currentPosition->y;
    }
}


function printMap(): int
{
    global $map, $currentPosition, $foundPosition;

    $unknown = 0;

    $extents = getMapExtents($map);
    for ($row = $extents[0]; $row <= $extents[1]; $row++) {
        for ($col = $extents[2]; $col <= $extents[3]; $col++) {
            if ($row === 0 && $col === 0) {
                print 'O';
            } elseif ($row === $currentPosition->y && $col === $currentPosition->x) {
                print 'D';
            } elseif ($row === $foundPosition->y && $col === $foundPosition->x) {
                print 'T';
            } elseif (array_key_exists($col, $map)
                && array_key_exists($row, $map[$col])) {
                print $map[$col][$row];
            } else {
                print '?';
                $unknown++;
            }
        }

        print "\n";
    }

    $mapSize = abs($extents[1] - $extents[0]) * abs($extents[3] * $extents[2]);
    print "\nUnknown spaces: $unknown out of $mapSize\n";
    return $unknown;
}

function getMapExtents(array $map): array
{
    $minRow = $minCol = PHP_INT_MAX;
    $maxRow = $maxCol = PHP_INT_MIN;

    foreach ($map as $col => $rows) {
        if ($col < $minCol) {
            $minCol = $col;
        }

        if ($col > $maxCol) {
            $maxCol = $col;
        }

        foreach ($rows as $row => $content) {
            if ($row < $minRow) {
                $minRow = $row;
            }

            if ($row > $maxRow) {
                $maxRow = $row;
            }
        }
    }

    return [
        $minRow,
        $maxRow,
        $minCol,
        $maxCol
    ];
}

function chooseNextDirection(): int
{
    global $map, $currentPosition;

    $possibleDirections = [];

    if (!array_key_exists($currentPosition->y - 1, $map[$currentPosition->x])) {
        $possibleDirections[MOVE_NORTH] = '';
    } elseif ($map[$currentPosition->x][$currentPosition->y - 1] === ' ') {
        $possibleDirections[MOVE_NORTH] = '';
    }

    if (!array_key_exists($currentPosition->y + 1, $map[$currentPosition->x])) {
        $possibleDirections[MOVE_SOUTH] = '';
    } elseif ($map[$currentPosition->x][$currentPosition->y + 1] === ' ') {
        $possibleDirections[MOVE_SOUTH] = '';
    }

    if (!array_key_exists($currentPosition->x + 1, $map)) {
        $possibleDirections[MOVE_EAST] = '';
    } elseif (!array_key_exists($currentPosition->y, $map[$currentPosition->x + 1])) {
        $possibleDirections[MOVE_EAST] = '';
    } elseif ($map[$currentPosition->x + 1][$currentPosition->y] === ' ') {
        $possibleDirections[MOVE_EAST] = '';
    }

    if (!array_key_exists($currentPosition->x - 1, $map)) {
        $possibleDirections[MOVE_WEST] = '';
    } elseif (!array_key_exists($currentPosition->y, $map[$currentPosition->x - 1])) {
        $possibleDirections[MOVE_WEST] = '';
    } elseif ($map[$currentPosition->x - 1][$currentPosition->y] === ' ') {
        $possibleDirections[MOVE_WEST] = '';
    }

    return array_rand($possibleDirections);
}

function findShortestPath(): int
{
    global $map, $foundPosition, $originPosition;

    $explored = [];

    /** @var Coord[] $currentNodes */
    $currentNodes = [$originPosition];
    $explored[] = $originPosition->getCoordAsString();
    $distance = 0;

    while (true) {
        $distance++;
        $newNodes = [];
        foreach ($currentNodes as $node) {
            foreach (getAdjacentSpaces($map, $node) as $newNode) {
                if ($newNode->x === $foundPosition->x && $newNode->y === $foundPosition->y) {
                    // This is the first time we've reached the target, so this is the shortest distance
                    return $distance;
                }

                if (!in_array($newNode->getCoordAsString(), $explored)) {
                    $newNodes[] = $newNode;
                    $explored[] = $newNode->getCoordAsString();
                }
            }
        }

        if (count($newNodes) === 0) {
            // we found no new unexplored nodes, and we've not reached the target
            throw new Exception('No path found to target');
        }

        // Set our new nodes to be the current nodes, and repeat
        $currentNodes = $newNodes;
    }
}

function getAdjacentSpaces(array $map, Coord $target): array
{
    $freeSpaces = [];
    if ($map[$target->x][$target->y - 1] === ' ') {
        $freeSpaces[] = new Coord($target->x, $target->y - 1);
    }

    if ($map[$target->x][$target->y + 1] === ' ') {
        $freeSpaces[] = new Coord($target->x, $target->y + 1);
    }

    if ($map[$target->x - 1][$target->y] === ' ') {
        $freeSpaces[] = new Coord($target->x - 1, $target->y);
    }

    if ($map[$target->x + 1][$target->y] === ' ') {
        $freeSpaces[] = new Coord($target->x + 1, $target->y);
    }

    return $freeSpaces;
}

function floodOxygen(): int
{
    global $map, $foundPosition;

    $map[$foundPosition->x][$foundPosition->y] = 'O';

    $steps = 0;
    while (mapHasSpaces()) {
        addOxygen();
        $steps++;
    }

    return $steps;
}

function mapHasSpaces(): bool
{
    global $map;
    $spaces = 0;

    foreach ($map as $col => $rows) {
        foreach ($rows as $row => $content) {
            if ($content === ' ') {
                $spaces++;
            }
        }
    }

    return $spaces > 0;
}

function addOxygen()
{
    global $map;

    foreach ($map as $x => $rows) {
        foreach ($rows as $y => $content) {
            if ($content === 'O') {
                foreach (getAdjacentSpaces($map, new Coord($x, $y)) as $adjacentSpace) {
                    $map[$adjacentSpace->x][$adjacentSpace->y] = 'O';
                }
            }
        }
    }
}

class Coord {
    public int $x = 0;
    public int $y = 0;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @return string
     */
    public function getCoordAsString(): string
    {
        return $this->x . ',' . $this->y;
    }
}

class Node extends Coord
{
    public ?Node $parentNode;
    public ?Node $childNode;

    public function __construct(int $x, int $y, Node $parentNode = null)
    {
        parent::__construct($x, $y);

        $this->parentNode = $parentNode;
        if ($parentNode instanceof Node) {
            $parentNode->childNode = $this;
        }
    }

    public static function createFromCoord(Coord $coord, Node $parentNode = null)
    {
        return new Node($coord->row, $coord->col, $parentNode);
    }
}
