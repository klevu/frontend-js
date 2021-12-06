<?php

namespace Klevu\FrontendJs\Test\Unit\Service;

use Klevu\FrontendJs\Service\IfConfigEvaluator;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class IfConfigEvaluatorTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function testEvaluate_In_Matches()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = 'foo';
        $conditions = [
            'in' => ['foo', 'bar', 'baz'],
        ];

        $this->assertTrue($ifConfigEvaluator->execute($value, $conditions));
    }

    public function testEvaluate_In_NotMatches()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = 'wom';
        $conditions = [
            'in' => ['foo', 'bar', 'baz'],
        ];

        $this->assertFalse($ifConfigEvaluator->execute($value, $conditions));
    }

    public function testEvaluate_In_NotArray()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = 'foo';
        $conditions = [
            'in' => 'bar',
        ];

        $this->assertFalse($ifConfigEvaluator->execute($value, $conditions));
    }

    public function testEvaluate_Nin_Matches()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = 'wom';
        $conditions = [
            'nin' => ['foo', 'bar', 'baz'],
        ];

        $this->assertTrue($ifConfigEvaluator->execute($value, $conditions));
    }

    public function testEvaluate_Nin_NotMatches()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

    }

    public function testEvaluate_Nin_NotArray()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = 'foo';
        $conditions = [
            'nin' => 'bar',
        ];

        $this->assertFalse($ifConfigEvaluator->execute($value, $conditions));
    }

    public function testEvaluate_Eq_Matches()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = 'foo';
        $conditions = [
            'eq' => 'foo',
        ];

        $this->assertTrue(
            $ifConfigEvaluator->execute($value, $conditions),
            'Same type comparison'
        );

        $value = '42';
        $conditions = [
            'eq' => 42.0,
        ];

        $this->assertTrue(
            $ifConfigEvaluator->execute($value, $conditions),
            'Type juggled comparison'
        );
    }

    public function testEvaluate_Eq_NotMatches()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = 'foo';
        $conditions = [
            'eq' => 'bar',
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Same type comparison'
        );

        $value = '0';
        $conditions = [
            'eq' => null,
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Type juggled comparison'
        );
    }

    public function testEvaluate_Neq_Matches()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = 'foo';
        $conditions = [
            'neq' => 'bar',
        ];

        $this->assertTrue(
            $ifConfigEvaluator->execute($value, $conditions),
            'Same type comparison'
        );

        $value = '0';
        $conditions = [
            'neq' => '',
        ];

        $this->assertTrue(
            $ifConfigEvaluator->execute($value, $conditions),
            'Falsy comparison'
        );
    }

    public function testEvaluate_Neq_NotMatches()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = 'foo';
        $conditions = [
            'neq' => 'foo',
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Same type comparison'
        );

        $value = '0';
        $conditions = [
            'neq' => 0,
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Type juggled comparison'
        );
    }

    public function testEvaluate_Gt_Matches()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = 50;
        $conditions = [
            'gt' => 49,
        ];

        $this->assertTrue(
            $ifConfigEvaluator->execute($value, $conditions),
            'Int comparison'
        );

        $value = 50.001;
        $conditions = [
            'gt' => 50.0,
        ];

        $this->assertTrue(
            $ifConfigEvaluator->execute($value, $conditions),
            'Float comparison'
        );

        $value = '50';
        $conditions = [
            'gt' => '49',
        ];

        $this->assertTrue(
            $ifConfigEvaluator->execute($value, $conditions),
            'String comparison'
        );

        $value = '50';
        $conditions = [
            'gt' => 49.5,
        ];

        $this->assertTrue(
            $ifConfigEvaluator->execute($value, $conditions),
            'Type juggled comparison'
        );
    }

    public function testEvaluate_Gt_NotMatches()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = 49;
        $conditions = [
            'gt' => 50,
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Int comparison'
        );

        $value = 50.0;
        $conditions = [
            'gt' => 50.001,
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Float comparison'
        );

        $value = '49';
        $conditions = [
            'gt' => '50',
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'String comparison'
        );

        $value = '49.5';
        $conditions = [
            'gt' => 50,
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Type juggled comparison'
        );
    }

    public function testEvaluate_Gt_NotScalar()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = ['foo', 'bar'];
        $conditions = [
            'gt' => 50,
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Array value'
        );

        $value = 50;
        $conditions = [
            'gt' => ['foo', 'bar'],
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Array condition'
        );

        $value = null;
        $conditions = [
            'gt' => -1
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Null value'
        );

        $value = 1;
        $conditions = [
            'gt' => null
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Null condition'
        );

        $value = true;
        $conditions = [
            'gt' => 0
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Boolean value'
        );

        $value = 1;
        $conditions = [
            'gt' => false
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Boolean condition'
        );
    }

    public function testEvaluate_Lt_Matches()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = 49;
        $conditions = [
            'lt' => 50,
        ];

        $this->assertTrue(
            $ifConfigEvaluator->execute($value, $conditions),
            'Int comparison'
        );

        $value = 50.0;
        $conditions = [
            'lt' => 50.001,
        ];

        $this->assertTrue(
            $ifConfigEvaluator->execute($value, $conditions),
            'Float comparison'
        );

        $value = '49';
        $conditions = [
            'lt' => '50',
        ];

        $this->assertTrue(
            $ifConfigEvaluator->execute($value, $conditions),
            'String comparison'
        );

        $value = '49.5';
        $conditions = [
            'lt' => 50,
        ];

        $this->assertTrue(
            $ifConfigEvaluator->execute($value, $conditions),
            'Type juggled comparison'
        );
    }

    public function testEvaluate_Lt_NotMatches()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = 50;
        $conditions = [
            'lt' => 49,
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Int comparison'
        );

        $value = 50.001;
        $conditions = [
            'lt' => 50.0,
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Float comparison'
        );

        $value = '50';
        $conditions = [
            'lt' => '49',
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'String comparison'
        );

        $value = '50';
        $conditions = [
            'lt' => 49.5,
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Type juggled comparison'
        );
    }

    public function testEvaluate_Lt_NotScalar()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = ['foo', 'bar'];
        $conditions = [
            'lt' => 50,
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Array value'
        );

        $value = 50;
        $conditions = [
            'lt' => ['foo', 'bar'],
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Array condition'
        );

        $value = null;
        $conditions = [
            'lt' => 1
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Null value'
        );

        $value = -1;
        $conditions = [
            'lt' => null
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Null condition'
        );

        $value = false;
        $conditions = [
            'lt' => 1
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Boolean value'
        );

        $value = 0;
        $conditions = [
            'lt' => true
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Boolean condition'
        );
    }

    public function testEvaluate_Combination_Matches()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = '50';
        $conditions = [
            'gt' => 49,
            'lt' => 51,
        ];

        $this->assertTrue(
            $ifConfigEvaluator->execute($value, $conditions),
            'Numeric'
        );

        $value = 'foo';
        $conditions = [
            'in' => ['foo', 'bar', 'baz'],
            'nin' => ['wom', 'bat'],
        ];

        $this->assertTrue(
            $ifConfigEvaluator->execute($value, $conditions),
            'Array'
        );
    }

    public function testEvaluate_Combination_NotMatches()
    {
        $this->setupPhp5();
        /** @var IfConfigEvaluator $ifConfigEvaluator */
        $ifConfigEvaluator = $this->objectManager->getObject(IfConfigEvaluator::class);

        $value = '50';
        $conditions = [
            'gt' => 49,
            'lt' => 50,
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'Numeric'
        );

        $value = 'foo';
        $conditions = [
            'in' => ['foo', 'bar', 'baz'],
            'neq' => 'foo',
        ];

        $this->assertFalse(
            $ifConfigEvaluator->execute($value, $conditions),
            'String'
        );
    }

    /**
     * @return void
     * @todo Move to setUp when PHP 5.x is no longer supported
     */
    private function setupPhp5()
    {
        $this->objectManager = new ObjectManager($this);
    }
}