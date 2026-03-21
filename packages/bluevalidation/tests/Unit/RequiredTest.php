<?php

namespace VerteXVaaR\BlueValidation\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use stdClass;
use VerteXVaaR\BlueValidation\Rule\Required;
use VerteXVaaR\BlueValidation\ValidationError;

use function fclose;
use function fopen;
use function fseek;

use const SEEK_END;

#[CoversClass(Required::class)]
#[UsesClass(ValidationError::class)]
class RequiredTest extends TestCase
{
    public static function emptyValuesDataProvider(): array
    {
        return [
            'null' => [null, 'required.but.null'],
            'string' => ['', 'required.but.empty_string'],
            'empty string' => ['   ', 'required.but.empty_string'],
            'string zero' => ['0', 'required.but.zero'],
            'zero int' => [0, 'required.but.zero'],
            'zero float' => [0.0, 'required.but.zero'],
            'empty object' => [new stdClass(), 'required.but.empty_object'],
            'array' => [[], 'required.but.empty_array'],
            'empty array' => [[[null, [0, ['', []]]]], 'required.but.empty_array'],
            'eof resource' => [
                (static function () {
                    $stream = fopen('data://text/plain,foo', 'rb');
                    fseek($stream, 0, SEEK_END);
                    return $stream;
                })(),
                'required.but.empty_resource',
            ],
            'closed resource' => [
                (static function () {
                    $stream = fopen('php://temp', 'r+b');
                    fclose($stream);
                    return $stream;
                })(),
                'required.but.closed_resource',
            ],
        ];
    }

    #[Test]
    #[DataProvider('emptyValuesDataProvider')]
    public function validationFailsForEmptyValues(mixed $value, string $expectedLabel): void
    {
        $required = new Required();
        $validationResult = $required->validate($value);
        self::assertSame($expectedLabel, $validationResult->label);
    }

    public static function nonEmptyValuesDataProvider(): array
    {
        return [
            'true' => [true],
            'false' => [false],
            'string chars' => ['asdf'],
            'string one' => ['1'],
            'integer' => [1],
            'float' => [1.1],
            'object' => [
                (static function (): object {
                    $object = new stdClass();
                    $object->foo = 'bar';
                    return $object;
                })(),
            ],
            'array' => [[1]],
            'resource' => [fopen('data://text/plain,foo', 'rb')],
        ];
    }

    #[Test]
    #[DataProvider('nonEmptyValuesDataProvider')]
    public function validationSucceedsForNonEmptyValues(mixed $value): void
    {
        $required = new Required();
        $validationResult = $required->validate($value);
        self::assertNull($validationResult);
    }
}
