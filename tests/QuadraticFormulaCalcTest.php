<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

use App\Exceptions\InvalidArgumentException;
use App\QuadraticFormulaCalc;
use PHPUnit\Framework\TestCase;

final class QuadraticFormulaCalcTest extends TestCase
{
    protected QuadraticFormulaCalc $calculator;

    public function setUp(): void
    {
        $this->calculator = new QuadraticFormulaCalc();
    }

    public function testLeadingCoefficientException()
    {
        $this->calculator->solve(PHP_FLOAT_EPSILON, 1);
        $this->expectException(InvalidArgumentException::class);
        $this->calculator->solve(0, 1);
    }

    public function testNoRoots()
    {
        $roots = $this->calculator->solve(1, 0, 1);
        $this->assertCount(0, $roots);
    }

    public function testSolveReturnsTwoRealSquereRoots()
    {
        $roots = $this->calculator->solve(1, 0, -1);
        $this->assertCount(2, $roots);
        $this->assertEquals(1, $roots[0]);
        $this->assertEquals(-1, $roots[1]);
    }

    public function testSolveReturnsOneRootOfMultiplicity2()
    {
        $roots = $this->calculator->solve(1, 2, 1);
        $this->assertCount(1, $roots);
        $this->assertEquals(-1, $roots[0]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSolveThrowsExceptionForSpecialNumbers()
    {
        foreach ([NAN, INF] as $specialValue) {
            for ($i = 0; $i < 3; $i++) {
                $arguments = [1, 1, 2, $i => $specialValue];
                try {
                    call_user_func_array([$this->calculator, 'solve'], $arguments);
                    $errorMessage = sprintf("Argument %d: special number %s does not throw an exception", $i + 1, $specialValue);
                    $this->fail($errorMessage);
                } catch (\InvalidArgumentException $e) {
                }
            }
        }
    }
}
