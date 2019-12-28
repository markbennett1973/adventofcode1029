<?php

declare(strict_types = 1);

const INPUT_FILE = 'day14input.txt';

echo 'Part 1: ' . part1() . "\n";
echo 'Part 2: ' . part2() . "\n";

function part1(): int
{
    $reactions = getInput();

    /** @var Reagent[] $requirements */
    $requirements[] = new Reagent('1 FUEL');

    while (!isReactionComplete($requirements)) {
        foreach ($requirements as $index => $requirement) {
            // No reactions can product ORE...
            if ($requirement->compound === 'ORE') {
                continue;
            }

            $reaction = findReactionForProduct($reactions, $requirement);
            $qtyRequired = getQtyRequired($requirement, $reaction);
            addRequirements($requirements, $reaction, $qtyRequired);
            removeProducts($requirements, $reaction, $qtyRequired);
        }
    }

    foreach ($requirements as $requirement) {
        if ($requirement->compound === 'ORE') {
            return $requirement->quantity;
        }
    }

    throw new Exception('No ORE requirement left!');
}

function part2(): string
{
    return '';
}

function getInput(): array
{
    $reactions = [];
    $input = file_get_contents(INPUT_FILE);
    foreach (explode("\n", $input) as $line) {
        if ($line) {
            $reactions[] = new Reaction($line);
        }
    }

    return $reactions;
}

/**
 * @param array|Reaction[] $reactions
 * @param Reagent $requirement
 * @return Reaction|null
 */
function findReactionForProduct(array $reactions, Reagent $requirement): ?Reaction
{
    foreach ($reactions as $reaction) {
        if ($reaction->productReagent->compound === $requirement->compound) {
            return $reaction;
        }
    }

    return null;
}

function getQtyRequired(Reagent $requirement, Reaction $reaction): int
{
    $qtyRequired = $requirement->quantity;
    $qtyProduced = $reaction->productReagent->quantity;

    return (int) ceil($qtyRequired / $qtyProduced);
}

/**
 * @param array|Reagent $requirements
 * @param Reaction $reaction
 * @param int $qtyRequired
 */
function addRequirements(array &$requirements, Reaction $reaction, int $qtyRequired)
{
    foreach ($reaction->sourceReagents as $sourceReagent) {
        // See if we already have this compound in our requirements
        $found = false;

        foreach ($requirements as $index => $requirement) {
            if ($requirement->compound === $sourceReagent->compound) {
                $found = true;
                break;
            }
        }

        if ($found) {
            $requirements[$index]->quantity = $requirements[$index]->quantity + ($sourceReagent->quantity * $qtyRequired);
        } else {
            $newReagentString = sprintf(
                '%d %s',
                $sourceReagent->quantity * $qtyRequired,
                $sourceReagent->compound
            );
            $requirements[] = new Reagent($newReagentString);
        }
    }
}

/**
 * @param array|Reagent[] $requirements
 * @param Reaction $reaction
 * @param int $qtyRequired
 */
function removeProducts(array &$requirements, Reaction $reaction, int $qtyRequired)
{
    foreach ($requirements as $index => $reagent) {
        if ($reagent->compound === $reaction->productReagent->compound) {
            $newQty = $reagent->quantity - ($reaction->productReagent->quantity * $qtyRequired);
            $requirements[$index]->quantity = $newQty;
        }
    }
}

/**
 * @param array|Reagent[] $requirements
 * @return bool
 */
function isReactionComplete(array &$requirements): bool
{
    foreach ($requirements as $requirement) {
        if ($requirement->compound === 'ORE') {
            continue;
        }

        if ($requirement->quantity > 0) {
            return false;
        }
    }

    return true;
}

class Reaction {
    /** @var array|Reagent[] */
    public array $sourceReagents;
    public Reagent $productReagent;

    /**
     * Reaction constructor.
     * @param string $reactionString
     *   e.g. 1 BPJLM, 1 HPBQ, 3 HVHN => 6 NLBM
     */
    public function __construct(string $reactionString)
    {
        $sections = explode('=>', $reactionString);

        $this->productReagent = new Reagent($sections[1]);

        $sources = explode(',', $sections[0]);
        foreach ($sources as $source) {
            $this->sourceReagents[] = new Reagent($source);
        }
    }
}

class Reagent {
    public string $compound;
    public int $quantity;

    /**
     * Reagent constructor.
     * @param string $compoundString
     *   e.g. 5 ABC
     */
    public function __construct(string $compoundString)
    {
        $parts = explode(' ', trim($compoundString));
        $this->compound = trim($parts[1]);
        $this->quantity = (int) $parts[0];
    }
}
