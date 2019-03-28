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
use App\Rst\RstParser;

class Replacement extends CheckListRule implements Rule
{
    public static function getGroups(): array
    {
        return [RulesHandler::GROUP_SONATA, RulesHandler::GROUP_SYMFONY];
    }

    public function check(\ArrayIterator $lines, int $number)
    {
        $lines->seek($number);
        $line = $lines->current();

        if (preg_match($this->pattern, RstParser::clean($line), $matches)) {
            return sprintf($this->message, $matches[0]);
        }
    }

    public function getDefaultMessage(): string
    {
        return 'Please don\'t use: %s';
    }

    public static function getList(): array
    {
        return [
            '/^([\s]+)?\/\/.\.(\.)?$/' => 'Please replace "%s" with "// ..."',
            '/^([\s]+)?#.\.(\.)?$/' => 'Please replace "%s" with "# ..."',
            '/^([\s]+)?<!--(.\.(\.)?|[\s]+\.\.[\s]+)-->$/' => 'Please replace "%s" with "<!-- ... -->"',
            '/^([\s]+)?{#(.\.(\.)?|[\s]+\.\.[\s]+)#}$/' => 'Please replace "%s" with "{# ... #}"',
            '/apps/' => 'Please replace "%s" with "applications"',
            '/Apps/' => 'Please replace "%s" with "Applications"',
            '/encoding="utf-8"/' => 'Please replace "%s" with "encoding="UTF-8""',
        ];
    }
}
