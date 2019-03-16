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

namespace App\Rule;

use App\Handler\RulesHandler;

class Typo extends CheckListRule
{
    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA, RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (strstr($line, $search = $this->search)) {
            return $this->message;
        }
    }

    public static function getList(): array
    {
        return [
            ['compsoer', 'Typo in word "%s"'],
            ['registerbundles()', 'Typo in word "%s", use "registerBundles()"'],
            ['retun', 'Typo in word "%s"'],
            ['displayes', 'Typo in word "%s"'],
            ['mantains',  'Typo in word "%s"'],
            ['doctine',  'Typo in word "%s"'],
        ];
    }
}
