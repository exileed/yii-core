<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\tests\framework\base;

use yii\base\Behavior;
use yii\base\Component;
use yii\tests\TestCase;
use yii\exceptions\UnknownMethodException;

class BarClass extends Component
{
}

class FooClass extends Component
{
    public function behaviors()
    {
        return [
            'foo' => __NAMESPACE__ . '\BarBehavior',
        ];
    }
}

class BarBehavior extends Behavior
{
    public static $attachCount = 0;
    public static $detachCount = 0;

    public $behaviorProperty = 'behavior property';

    public function behaviorMethod()
    {
        return 'behavior method';
    }

    public function __call($name, $params)
    {
        if ($name == 'magicBehaviorMethod') {
            return 'Magic Behavior Method Result!';
        }

        return parent::__call($name, $params);
    }

    public function hasMethod($name)
    {
        if ($name == 'magicBehaviorMethod') {
            return true;
        }

        return parent::hasMethod($name);
    }

    public function attach($owner)
    {
        self::$attachCount++;
        parent::attach($owner);
    }

    public function detach()
    {
        self::$detachCount++;
        parent::detach();
    }
}

/**
 * @group base
 */
class BehaviorTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->mockApplication();
    }

    protected function tearDown()
    {
        parent::tearDown();
        gc_enable();
        gc_collect_cycles();
    }

    public function testAttachAndAccessingWithName()
    {
        BarBehavior::$attachCount = 0;
        BarBehavior::$detachCount = 0;

        $bar = $this->app->createObject(BarClass::class);
        $behavior = new BarBehavior();
        $bar->attachBehavior('bar', $behavior);
        $this->assertEquals(1, BarBehavior::$attachCount);
        $this->assertEquals(0, BarBehavior::$detachCount);
        $this->assertEquals('behavior property', $bar->behaviorProperty);
        $this->assertEquals('behavior method', $bar->behaviorMethod());
        $this->assertEquals('behavior property', $bar->getBehavior('bar')->behaviorProperty);
        $this->assertEquals('behavior method', $bar->getBehavior('bar')->behaviorMethod());

        $behavior = $this->app->createObject([
            '__class' => BarBehavior::class,
            'behaviorProperty' => 'reattached',
        ]);
        $bar->attachBehavior('bar', $behavior);
        $this->assertEquals(2, BarBehavior::$attachCount);
        $this->assertEquals(1, BarBehavior::$detachCount);
        $this->assertEquals('reattached', $bar->behaviorProperty);
    }

    public function testAttachAndAccessingAnonymous()
    {
        BarBehavior::$attachCount = 0;
        BarBehavior::$detachCount = 0;

        $bar = $this->app->createObject(BarClass::class);
        $behavior = new BarBehavior();
        $bar->attachBehaviors([$behavior]);
        $this->assertEquals(1, BarBehavior::$attachCount);
        $this->assertEquals(0, BarBehavior::$detachCount);
        $this->assertEquals('behavior property', $bar->behaviorProperty);
        $this->assertEquals('behavior method', $bar->behaviorMethod());
    }

    public function testAutomaticAttach()
    {
        BarBehavior::$attachCount = 0;
        BarBehavior::$detachCount = 0;

        $foo = $this->app->createObject(FooClass::class);
        $this->assertEquals(0, BarBehavior::$attachCount);
        $this->assertEquals(0, BarBehavior::$detachCount);
        $this->assertEquals('behavior property', $foo->behaviorProperty);
        $this->assertEquals('behavior method', $foo->behaviorMethod());
        $this->assertEquals(1, BarBehavior::$attachCount);
        $this->assertEquals(0, BarBehavior::$detachCount);
    }

    public function testMagicMethods()
    {
        $bar = $this->app->createObject(BarClass::class);
        $behavior = new BarBehavior();

        $this->assertFalse($bar->hasMethod('magicBehaviorMethod'));
        $bar->attachBehavior('bar', $behavior);
        $this->assertFalse($bar->hasMethod('magicBehaviorMethod', false));
        $this->assertTrue($bar->hasMethod('magicBehaviorMethod'));

        $this->assertEquals('Magic Behavior Method Result!', $bar->magicBehaviorMethod());
    }

    public function testCallUnknownMethod()
    {
        $bar = $this->app->createObject(BarClass::class);
        $behavior = new BarBehavior();
        $this->expectException(UnknownMethodException::class);

        $this->assertFalse($bar->hasMethod('nomagicBehaviorMethod'));
        $bar->attachBehavior('bar', $behavior);
        $bar->nomagicBehaviorMethod();
    }
}
