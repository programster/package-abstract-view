<?php

/*
 * AbstractView: rendering to a string, and leaving no output or open buffer behind when a view goes wrong.
 * These are run by testing/main.php, which provides $smoke.
 */

use Programster\AbstractView\AbstractView;
use Programster\AbstractView\Testing\Fixtures\CallbackView;
use Programster\AbstractView\Testing\SmokeTest;

/** @var SmokeTest $smoke */

$outputHello = function () : void {
    echo 'hello';
};

$outputThenThrow = function () : void {
    echo 'half-rendered';
    throw new RuntimeException('view failed');
};

/**
 * Run a check inside a buffer of its own, and check it printed nothing and left the buffers as they were.
 */
$checkLeavesNothing = function (Closure $check) : void {
    $level = ob_get_level();
    ob_start();

    try
    {
        $check();
        SmokeTest::assertSame($level + 1, ob_get_level(), 'the open output buffers');
        SmokeTest::assertSame('', (string)ob_get_contents(), 'what was printed');
    }
    finally
    {
        while (ob_get_level() > $level)
        {
            ob_end_clean();
        }
    }
};

$testRender = function () use ($outputHello, $checkLeavesNothing) : void {
    $check = function () use ($outputHello) : void {
        SmokeTest::assertSame('hello', (new CallbackView($outputHello))->render(), 'the rendered view');
    };

    $checkLeavesNothing($check);
};

$smoke->run('render() returns the output without printing it', $testRender);

$testToString = function () use ($outputHello, $checkLeavesNothing) : void {
    $check = function () use ($outputHello) : void {
        SmokeTest::assertSame('hello', (string)new CallbackView($outputHello), 'the view as a string');
    };

    $checkLeavesNothing($check);
};

$smoke->run('a view can be used as a string', $testToString);

$testNested = function () use ($outputHello, $checkLeavesNothing) : void {
    $check = function () use ($outputHello) : void {
        $inner = new CallbackView($outputHello);

        $outputOuter = function () use ($inner) : void {
            echo "<main>{$inner}</main>";
        };

        SmokeTest::assertSame('<main>hello</main>', (new CallbackView($outputOuter))->render(), 'the nested views');
    };

    $checkLeavesNothing($check);
};

$smoke->run('views can be nested', $testNested);

$testThrow = function () use ($outputThenThrow, $checkLeavesNothing) : void {
    $check = function () use ($outputThenThrow) : void {
        $render = function () use ($outputThenThrow) : void {
            (new CallbackView($outputThenThrow))->render();
        };

        SmokeTest::expectException(RuntimeException::class, $render, 'view failed');
    };

    $checkLeavesNothing($check);
};

$smoke->run('a view that throws leaks no output or buffer, and the exception is passed on', $testThrow);

$testNestedThrow = function () use ($outputThenThrow, $checkLeavesNothing) : void {
    $check = function () use ($outputThenThrow) : void {
        $inner = new CallbackView($outputThenThrow);

        $outputOuter = function () use ($inner) : void {
            echo '<aside>sidebar</aside>';
            echo $inner->render();
        };

        $render = function () use ($outputOuter) : void {
            (new CallbackView($outputOuter))->render();
        };

        SmokeTest::expectException(RuntimeException::class, $render, 'view failed');
    };

    $checkLeavesNothing($check);
};

$smoke->run('a nested view that throws leaks none of the outer view', $testNestedThrow);

$testThrowWithOwnBuffer = function () use ($checkLeavesNothing) : void {
    $check = function () : void {
        $outputInOwnBuffer = function () : void {
            ob_start();
            echo 'inner';
            throw new RuntimeException('view failed');
        };

        $render = function () use ($outputInOwnBuffer) : void {
            (new CallbackView($outputInOwnBuffer))->render();
        };

        SmokeTest::expectException(RuntimeException::class, $render, 'view failed');
    };

    $checkLeavesNothing($check);
};

$smoke->run('a buffer opened by a view that throws is closed too', $testThrowWithOwnBuffer);

$testForgottenBuffer = function () use ($checkLeavesNothing) : void {
    $check = function () : void {
        $outputAndForget = function () : void {
            echo 'before ';
            ob_start();
            echo 'after';
        };

        SmokeTest::assertSame('before after', (new CallbackView($outputAndForget))->render(), 'the rendered view');
    };

    $checkLeavesNothing($check);
};

$smoke->run('the output of a buffer the view forgot to close is kept', $testForgottenBuffer);

$testClosedBuffer = function () use ($checkLeavesNothing) : void {
    $check = function () : void {
        $closeBuffer = function () : void {
            ob_end_clean();
        };

        $render = function () use ($closeBuffer) : void {
            (new CallbackView($closeBuffer))->render();
        };

        SmokeTest::expectException(LogicException::class, $render, 'closed the output buffer');
    };

    $checkLeavesNothing($check);
};

$smoke->run('a view that closes the buffer it renders to is refused', $testClosedBuffer);

$testReturnTypes = function () : void {
    $renderType = (string)(new ReflectionMethod(AbstractView::class, 'render'))->getReturnType();
    $contentType = (string)(new ReflectionMethod(AbstractView::class, 'renderContent'))->getReturnType();
    SmokeTest::assertSame('string', $renderType, 'the return type of render()');
    SmokeTest::assertSame('void', $contentType, 'the return type of renderContent()');
};

$smoke->run('render() and renderContent() declare their return types', $testReturnTypes);
