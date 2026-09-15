<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific
| PHPUnit test case class. By default, that class is "PHPUnit\Framework\TestCase".
| Here you may override it with your project's own test case class.
|
*/

uses(Tests\TestCase::class)->in('Feature', 'Unit');