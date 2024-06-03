<?php

namespace App;

use App\Exceptions\InvalidArgumentException;

class QuadraticFormulaCalc
{
    /**
     * @param float $a
     * @param float $b
     * @param float $c
     * @throws InvalidArgumentException
     * @return float[]
     */
    public function solve(float $a, float $b = 0, float $c = 0): array
    {
        if (is_nan($a) || is_nan($b) || is_nan($c)) {
            throw new InvalidArgumentException('NaN not supported');
        }

        if (is_infinite($a) || is_infinite($b) || is_infinite($c)) {
            throw new InvalidArgumentException('Infinite numbers not supported');
        }

        if (abs($a) < PHP_FLOAT_EPSILON) {
            throw new InvalidArgumentException('Leading coefficient must be greater than 0');
        }

        $d = pow($b, 2) - 4 * $a * $c;

        if ($d < 0) {
            return [];
        }

        if ($d > 0) {
            return [
                (-$b + sqrt($d)) / 2 * $a,
                (-$b - sqrt($d)) / 2 * $a
            ];
        }

        if (abs($d) < PHP_FLOAT_EPSILON) {
            return [(-$b) / 2 * $a];
        }
    }
}
