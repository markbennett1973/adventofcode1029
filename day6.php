<?php

declare(strict_types = 1);

const INPUT_FILE = 'day6input.txt';

$bodies = getBodies();

echo 'Part 1: ' . countOrbits($bodies) . "\n";
echo 'Part 2: ' . getDistance($bodies, 'YOU', 'SAN') . "\n";
// part 1 = 249308

function getBodies(): array
{
    $input = file_get_contents(INPUT_FILE);
    /** @var Body[] $bodies */
    $bodies = [];

    foreach (explode("\n", $input) as $line) {
        $parts = explode(')', $line);
        if (count($parts) === 2) {
            $orbits = $parts[0];
            $name = $parts[1];

            if (!array_key_exists($orbits, $bodies)) {
                $bodies[$orbits] = new Body($orbits);
            }

            if (array_key_exists($name, $bodies)) {
                $bodies[$name]->orbits = $bodies[$orbits];
            } else {
                $bodies[$name] = new Body($name, $bodies[$orbits]);
            }
        }
    }

    return $bodies;
}

/**
 * @param array|Body[] $bodies
 * @return int
 */
function countOrbits($bodies): int
{
    $totalOrbits = 0;
    foreach ($bodies as $body) {
        $totalOrbits += $body->countOrbits();
    }

    return $totalOrbits;
}

/**
 * @param Body[] $bodies
 * @param string $start
 * @param string $destination
 * @return int
 */
function getDistance(array $bodies, string $start, string $destination): int
{
    $startBody = $bodies[$start];
    $endBody = $bodies[$destination];
    $commonAncestor = findCommonAncestor($startBody, $endBody);

    return $startBody->getDistanceTo($commonAncestor) + $endBody->getDistanceTo($commonAncestor);
}

function findCommonAncestor(Body $start, Body $end): ?Body
{
    $startBodyChain = $start->getOrbitsChain();

    $current = $end;
    while ($current = $current->orbits) {
        if (in_array($current->name, $startBodyChain)) {
            return $current;
        }
    }

    return null;
}

class Body
{
    public string $name;
    public ?Body $orbits;

    public function __construct(string $name, ?Body $orbits = null)
    {
        $this->name = $name;
        $this->orbits = $orbits;
    }

    public function countOrbits(): int
    {
        if ($this->orbits === null) {
            return 0;
        }

        return 1 + $this->orbits->countOrbits();
    }

    public function getOrbitsChain(): array
    {
        if ($this->orbits === null) {
            return [$this->name];
        } else {
            return array_merge([$this->name], $this->orbits->getOrbitsChain());
        }
    }

    public function getDistanceTo(Body $body): int
    {
        if ($this->orbits === $body) {
            return 0;
        }

        return 1 + $this->orbits->getDistanceTo($body);
    }
}