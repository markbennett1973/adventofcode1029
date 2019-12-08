<?php

declare(strict_types = 1);

const INPUT_FILE = 'day8input.txt';

const BLACK = 0;
const WHITE = 1;
const TRANSPARENT = 2;

echo 'Part 1: ' . part1() . "\n";
echo "Part 2:\n";
part2() ;

function part1(): int
{
    $image = getImage(25, 6);
    $minZeros = null;
    $minLayer = null;
    foreach ($image as $index => $layer) {
        $zeros = countDigitsInLayer($layer, 0);
        if ($minZeros === null || $zeros < $minZeros) {
            $minZeros = $zeros;
            $minLayer = $layer;
        }
    }

    return countDigitsInLayer($minLayer, 1) * countDigitsInLayer($minLayer, 2);
}

function part2()
{
    $image = getImage(25, 6);
    $resolvedImage = resolveImage($image, 25, 6);
    printImage($resolvedImage);
    return '';
}

function getImage(int $width, int $height): array
{
    $values = file_get_contents(INPUT_FILE);
    $layers = strlen($values) / ($width * $height);

    $image = [];
    $position = 0;
    for ($layer = 0; $layer < $layers; $layer++) {
        for ($row = 0; $row < $height; $row++) {
            for ($col = 0; $col < $width; $col++) {
                $image[$layer][$row][$col] = (int) substr($values, $position, 1);
                $position++;
            }
        }
    }

    return $image;
}

function countDigitsInLayer(array $layer, int $targetDigit): int
{
    $count = 0;
    foreach ($layer as $row => $cols) {
        foreach ($cols as $col => $digit) {
            if ($digit === $targetDigit) {
                $count++;
            }
        }
    }

    return $count;
}

function resolveImage(array $image, int $width, int $height): array
{
    $layers = count($image);
    $resolvedImage = [];

    for ($row = 0; $row < $height; $row++) {
        for ($col = 0; $col < $width; $col++) {
            for ($layer = 0; $layer < $layers; $layer++) {
                $pixel = $image[$layer][$row][$col];
                if ($pixel !== TRANSPARENT) {
                    $resolvedImage[$row][$col] = $pixel;
                    break;
                }
            }
        }
    }

    return $resolvedImage;
}

function printImage(array $image)
{
    foreach ($image as $row => $cols) {
        foreach ($cols as $col => $pixel) {
            print $pixel === BLACK ? '  ' : '@@';
        }
        print "\n";
    }
}