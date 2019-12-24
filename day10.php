<?php

declare(strict_types = 1);

const INPUT_FILE = 'day10input.txt';

const SPACE = 0;
const ASTEROID = 1;
const LASER = 2;

$map = getMap();
$bestCoords = getBestCoords($map);

// Part 1:
$visible = countVisibleFromPoint($map, $bestCoords[0], $bestCoords[1]);
print "$visible asteroids visible from " . $bestCoords[0] . ', ' . $bestCoords[1] . "\n";

// Part 2:
$laserRow = $bestCoords[0];
$laserCol = $bestCoords[1];
$map[$laserRow][$laserCol] = LASER;
$currentBearing = -1;
$destroyedCount = 0;
while (countAsteroids($map) > 0) {
    $destroyed = fireLaser($map, $laserRow, $laserCol, $currentBearing);
    $currentBearing = getBearing($laserRow, $laserCol, $destroyed[0], $destroyed[1]);
    $destroyedCount++;
    print "Removed $destroyedCount - " . $destroyed[1] . ', ' . $destroyed[0] . "\n";

}

function getMap(): array
{
    $map = [];
    $input = file_get_contents(INPUT_FILE);
    foreach (explode("\n", $input) as $row => $rowContent) {
        $cols = strlen($rowContent);
        for ($col = 0; $col < $cols; $col++) {
            $map[$row][$col] = $rowContent[$col] === '#' ? ASTEROID : SPACE;
        }
    }

    return $map;
}

function getBestCoords(array $map): array
{
    $maxViews = 0;
    $bestCoords = [];

    foreach ($map as $row => $cols) {
        foreach ($cols as $col => $hasAsteroid) {
            if ($hasAsteroid) {
                $views = countVisibleFromPoint($map, $row, $col);
                if ($views > $maxViews) {
                    $maxViews = $views;
                    $bestCoords = [$row, $col];
                }
            }
        }
    }

    return $bestCoords;
}

function countVisibleFromPoint(array $map, int $pointRow, int $pointCol): int
{
    $visible = 0;
    foreach ($map as $row => $colData) {
        foreach ($colData as $col => $hasAsteroid) {
            if ($hasAsteroid) {
                if (isAsteroidVisibleFromPoint($map, $row, $col, $pointRow, $pointCol)) {
                    $visible++;
                }
            }
        }
    }

    return $visible;
}

function isAsteroidVisibleFromPoint(array $map, $row, $col, $pointRow, $pointCol): bool
{
    // A point is not visible from itself
    if ($row === $pointRow && $col === $pointCol) {
        return false;
    }

    // If any intermediate cells contain an asteroid, then the destination is not visible
    $intermediateCells = calculateIntermediateCells($row, $col, $pointRow, $pointCol);
    foreach ($intermediateCells as $cell) {
        $cellRow = $cell[0];
        $cellCol = $cell[1];
        if ($map[$cellRow][$cellCol] === ASTEROID) {
            return false;
        }
    }

    return true;
}

/**
 * Calculate cells which lie exactly between two points
 * @param int $startRow
 * @param int $startCol
 * @param int $endRow
 * @param int $endCol
 * @return array
 *   0 => [row, col],
 *   1 => [row, col],
 *   2 => [row, col],
 *   ...
 */
function calculateIntermediateCells(int $startRow, int $startCol, int $endRow, int $endCol): array
{
    $intermediateCells = [];

    // Always check in a positive columns direction - makes the maths a bit simpler. So if $end < $start, swap them.
    if ($endCol < $startCol) {
        $tmp = $endCol;
        $endCol = $startCol;
        $startCol = $tmp;

        $tmp = $endRow;
        $endRow = $startRow;
        $startRow = $tmp;
    }

    $dRow = $endRow - $startRow;
    $dCol = $endCol - $startCol;

    // Special case for vertical lines - we can't calculate a gradient
    if ($dCol === 0) {
        return calculateVerticalIntermediateCells($startRow, $startCol, $endRow);
    }

    $gradient = $dRow / $dCol;
    // don't include our start and end points
    for ($col = $startCol + 1; $col < $endCol; $col++) {
        $row = $startRow + ($gradient * ($col - $startCol));
        // Check if we got an exact integer (accounting for float rounding)
        if (abs($row - round($row)) < 0.0001) {
            $intermediateCells[] = [(int) $row, $col];
        }
    }

    return $intermediateCells;
}


function calculateVerticalIntermediateCells(int $startRow, int $startCol, int $endRow): array
{
    // if start < end, swap them
    if ($startRow > $endRow) {
        $tmp = $endRow;
        $endRow = $startRow;
        $startRow = $tmp;
    }

    $intermediateCells = [];
    // don't include the start and end points
    for ($row = $startRow + 1; $row < $endRow; $row++) {
        $intermediateCells[] = [$row, $startCol];
    }

    return $intermediateCells;
}

function countAsteroids(array $map): int
{
    $count = 0;
    foreach ($map as $row => $cols) {
        foreach ($cols as $col => $content) {
            if ($map[$row][$col] === ASTEROID) {
                $count++;
            }
        }
    }

    return $count;
}

/**
 * Fire the laser from $laserRow, $laserCol. Find the first asteroid in $map where angle > $currentAngle
 * Remove asteroid from map
 * Return coordinates of removed asteroid
 *
 * @param array $map
 * @param int $laserRow
 * @param int $laserCol
 * @param $currentBearing
 * @return array
 */
function fireLaser(array &$map, int $laserRow, int $laserCol, $currentBearing): array
{
    $currentMilliBearing = $currentBearing * 1000;
    foreach ($map as $row => $cols) {
        foreach ($cols as $col => $contents) {
            if ($contents === 1) {
                if (isAsteroidVisibleFromPoint($map, $row, $col, $laserRow, $laserCol)) {
                    $milliBearing = getBearing($laserRow, $laserCol, $row, $col);
                    $milliBearing = $milliBearing * 1000;
                    $bearings[$milliBearing] = [$row, $col];
                }
            }
        }
    }

    // find first asteroid where angle > $currentAngle
    ksort($bearings);

    $nextMilliBearing = null;
    foreach ($bearings as $milliBearing => $asteroid) {
        if ($milliBearing > $currentMilliBearing) {
            $nextMilliBearing = $milliBearing;
            break;
        }
    }

    // We may need to wrap around if we're near 360 degrees and there are no following bearings
    if ($nextMilliBearing ===  null) {
        $currentMilliBearing -= 360000;
        foreach ($bearings as $milliBearing => $asteroid) {
            if ($milliBearing > $currentMilliBearing) {
                $nextMilliBearing = $milliBearing;
                break;
            }
        }
    }

    // remove from map
    $targetRow = $bearings[$nextMilliBearing][0];
    $targetCol = $bearings[$nextMilliBearing][1];
    $map[$targetRow][$targetCol] = 0;

    // return coordinates of removed asteroid
    return $bearings[$nextMilliBearing];
}

function getBearing($sourceRow, $sourceCol, $destRow, $destCol): float
{
    $dX = $destRow - $sourceRow;
    $dY = $destCol - $sourceCol;
    $bearing = rad2deg(atan2($dX, $dY));
    $bearing += 90;

    if ($bearing < 0) {
        $bearing += 360;
    }

    if ($bearing > 360) {
        $bearing -= 360;
    }

    return $bearing;
}

function testGetBearing() {
    print getBearing(0, 0, -1, 0) . " should be 0\n";
    print getBearing(0, 0, 0, 1) . " should be 90\n";
    print getBearing(0, 0, 1, 0) . " should be 180\n";
    print getBearing(0, 0, 0, -1) . " should be 270\n";
    print getBearing(0, 0, -1, 1) . " should be 45\n";
    print getBearing(0, 0, 1, 1) . " should be 135\n";
    print getBearing(0, 0, 1, -1) . " should be 225\n";
    print getBearing(0, 0, -1, -1) . " should be 315\n";
}
