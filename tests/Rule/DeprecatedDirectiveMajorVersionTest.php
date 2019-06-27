<?php

declare(strict_types=1);

/*
 * This file is part of DOCtor-RST.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Rule;

use App\Rule\DeprecatedDirectiveMajorVersion;
use App\Tests\RstSample;
use Composer\Semver\VersionParser;
use PHPUnit\Framework\TestCase;

class DeprecatedDirectiveMajorVersionTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider checkProvider
     */
    public function check(?string $expected, int $majorVersion, RstSample $sample)
    {
        $rule = (new DeprecatedDirectiveMajorVersion(new VersionParser()));
        $rule->setOptions(['major_version' => $majorVersion]);

        $this->assertSame($expected, $rule->check($sample->getContent(), $sample->getLineNumber()));
    }

    public function checkProvider()
    {
        return [
            [
                null,
                3,
                new RstSample('.. deprecated:: 3'),
            ],
            [
                null,
                3,
                new RstSample('.. deprecated:: 3.4'),
            ],
            [
                null,
                3,
                new RstSample('.. deprecated:: 3.4.0'),
            ],
            [
                null,
                3,
                new RstSample('.. deprecated:: 3.4.0.0'),
            ],
            [
                null,
                3,
                new RstSample('.. deprecated:: 3.4   '),
            ],
            [
                'You are not allowed to use version "2.7". Only major version "3" is allowed.',
                3,
                new RstSample('.. deprecated:: 2.7'),
            ],
            [
                'You are not allowed to use version "4.0". Only major version "3" is allowed.',
                3,
                new RstSample('.. deprecated:: 4.0'),
            ],
            [
                'Please provide a numeric version behind ".. deprecated::" instead of "foo"',
                3,
                new RstSample('.. deprecated:: foo'),
            ],
        ];
    }
}