<?php

declare(strict_types = 1);

$min = 382345;
$max = 843167;
$part1Matches = $part2Matches = 0;

for ($i = $min; $i <= $max; $i++) {
    if (isValidPart1($i)) {
        $part1Matches++;
    }

    if (isValidPart2($i)) {
        $part2Matches++;
    }
}

echo "Part 1: $part1Matches\n";
echo "Part 2: $part2Matches\n";

function isValidPart1(int $number): bool
{
    $string = (string) $number;

    // Check for increasing digits
    for ($pos = 1; $pos <= 5; $pos++) {
        // If the previous digit is less than the current digit, this number is not valid
        if ($string[$pos] < $string[$pos - 1]) {
            return false;
        }
    }

    // Check for the existence of a repeated digit
    for ($pos = 1; $pos <= 5; $pos++) {
        // If the previous digit is the same as the current digit, this number is valid
        if ($string[$pos] === $string[$pos - 1]) {
            return true;
        }
    }

    return false;
}


function isValidPart2(int $number): bool
{
    $string = (string) $number;

    // Check for increasing digits
    for ($pos = 1; $pos <= 5; $pos++) {
        // If the previous digit is less than the current digit, this number is not valid
        if ($string[$pos] < $string[$pos - 1]) {
            return false;
        }
    }

    // Check for a digit repeated twice
    $digits = array();
    for ($pos = 0; $pos <= 5; $pos++) {
        $digit = $string[$pos];
        if (array_key_exists($digit, $digits)) {
            $digits[$digit]++;
        } else {
            $digits[$digit] = 1;
        }
    }

    foreach ($digits as $digit => $occurrences) {
        if ($occurrences ===2 ) {
            return true;
        }
    }

    return false;
}


