<?php

declare(strict_types = 1);

const INPUT_FILE = 'day12input.txt';

echo 'Part 1: ' . part1() . "\n";
echo 'Part 2: ' . part2() . "\n";

function part1(): int
{
    $maxSteps = 1000;
    $moons = getInput();

    for ($step = 0; $step < $maxSteps; $step++) {
        moveAllMoons($moons);
    }

    $energy = 0;
    foreach ($moons as $moon) {
        $energy += $moon->getEnergy();
    }

    return $energy;
}

function part2(): int
{
    $periodX = getPeriod('x', getInput());
    $periodY = getPeriod('y', getInput());
    $periodZ = getPeriod('z', getInput());

    $lcm1 = lcm($periodX, $periodY);
    return lcm($lcm1, $periodZ);
}

function  lcm(int $a, int $b): int
{
    return $a * ($b / gcd($a, $b));
}

function gcd(int $a, int $b): int
{
    while ($b > 0)
    {
        $temp = $b;
        $b = $a % $b; // % is remainder
        $a = $temp;
    }
    return $a;
}

/**
 * @return array|Moon[]
 */
function getInput(): array
{
    $moons = [];
    $input = file_get_contents(INPUT_FILE);
    foreach (explode("\n", $input) as $line) {
        if ($line) {
            $moons[] = new Moon($line);
        }
    }

    return $moons;
}

/**
 * Get all the different pairs based on their array indices
 * @return array
 */
function getPairs(): array
{
    return [
        [0, 1],
        [0, 2],
        [0, 3],
        [1, 2],
        [1, 3],
        [2, 3],
    ];
}

/**
 * @param array|Moon[] $moons
 */
function moveAllMoons(array $moons)
{
    foreach (getPairs() as $pair) {
        $moons[$pair[0]]->updateVelocity($moons[$pair[1]]);
    }

    foreach ($moons as $moon) {
        $moon->move();
    }
}

/**
 * @param string $axis
 * @param array|Moon[] $moons
 * @return int
 */
function getPeriod(string $axis, array $moons): int
{
    $steps = 0;
    do
    {
         moveAllMoons($moons);
        $steps++;
    } while (!allAtStart($axis, $moons));

    return $steps;
}

/**
 * @param string $axis
 * @param array|Moon[] $moons
 * @return bool
 */
function allAtStart(string $axis, array $moons): bool
{
    foreach ($moons as $moon) {
        if (!$moon->isAtStart($axis)) {
            return false;
        }
    }

    return true;
}

function lowestCommonMultiple(array $periods): int
{
    // TODO
    return 0;
}

class Moon {
    private int $x;
    private int $y;
    private int $z;
    protected int $vx;
    protected int $vy;
    protected int $vz;
    private $initialStates;

    public function __construct(string $initialPosition)
    {
        // <x=-1, y=0, z=2>
        $initialPosition = str_replace(['<', '>', 'x', 'y', 'z', ' ', '='], [], $initialPosition);
        $coords = explode(',', $initialPosition);
        $this->x = (int) $coords[0];
        $this->y = (int) $coords[1];
        $this->z = (int) $coords[2];

        $this->vx = 0;
        $this->vy = 0;
        $this->vz = 0;

        $this->initialStates = [
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
            'vx' => $this->vx,
            'vy' => $this->vy,
            'vz' => $this->vz,
        ];
    }

    public function updateVelocity(Moon $moon)
    {
        if ($this->x > $moon->x) {
            $this->vx--;
            $moon->vx++;
        } elseif ($this->x < $moon->x) {
            $this->vx++;
            $moon->vx--;
        }

        if ($this->y > $moon->y) {
            $this->vy--;
            $moon->vy++;
        } elseif ($this->y < $moon->y) {
            $this->vy++;
            $moon->vy--;
        }

        if ($this->z > $moon->z) {
            $this->vz--;
            $moon->vz++;
        } elseif ($this->z < $moon->z) {
            $this->vz++;
            $moon->vz--;
        }
    }

    public function move()
    {
        $this->x += $this->vx;
        $this->y += $this->vy;
        $this->z += $this->vz;
    }

    public function getState(): string
    {
        return sprintf(
            "pos=<x= %d, y= %d, z= %d>, vel=<x= %d, y= %d, z= %d>\n",
            $this->x,
            $this->y,
            $this->z,
            $this->vx,
            $this->vy,
            $this->vz
        );
    }

    public function getEnergy(): int
    {
        $pot = abs($this->x) + abs($this->y) + abs($this->z);
        $kin = abs($this->vx) + abs($this->vy) + abs($this->vz);
        return $pot * $kin;
    }

    public function isAtStart(string $axis): bool
    {
        if ($this->{$axis} !== $this->initialStates[$axis]) {
            return false;
        }

        $velocityProperty = 'v' . $axis;
        if ($this->{$velocityProperty} !== $this->initialStates[$velocityProperty]) {
            return false;
        }

        return true;
    }
}
