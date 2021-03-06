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

use App\Handler\Registry;
use App\Rst\RstParser;
use App\Value\Lines;
use App\Value\RuleGroup;

class CorrectCodeBlockDirectiveBasedOnTheContent extends AbstractRule implements Rule
{
    public static function getGroups(): array
    {
        return [
            RuleGroup::fromString(Registry::GROUP_SONATA),
            RuleGroup::fromString(Registry::GROUP_SYMFONY),
        ];
    }

    public function check(Lines $lines, int $number): ?string
    {
        $lines->seek($number);
        $line = $lines->current();

        if (!RstParser::directiveIs($line, RstParser::DIRECTIVE_CODE_BLOCK)) {
            return null;
        }

        $indention = $line->indention();

        // check code-block: twig
        if (RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_TWIG, true)) {
            $lines->next();

            while ($lines->valid()
                && ($indention < $lines->current()->indention() || $lines->current()->isBlank())
            ) {
                if (preg_match('/[<]+/', $lines->current()->clean(), $matches)
                    && !preg_match('/<3/', $lines->current()->clean())
                ) {
                    return $this->getErrorMessage(RstParser::CODE_BLOCK_HTML_TWIG, RstParser::CODE_BLOCK_TWIG);
                }

                $lines->next();
            }
        }

        // check code-block: html+twig
        if (RstParser::codeBlockDirectiveIsTypeOf($line, RstParser::CODE_BLOCK_HTML_TWIG, true)) {
            $lines->next();

            $foundHtml = false;

            while ($lines->valid()
                && ($indention < $lines->current()->indention() || $lines->current()->isBlank())
                && false === $foundHtml
            ) {
                if (preg_match('/[<]+/', $lines->current()->clean())) {
                    $foundHtml = true;
                }

                $lines->next();
            }

            if (!$foundHtml) {
                return $this->getErrorMessage(RstParser::CODE_BLOCK_TWIG, RstParser::CODE_BLOCK_HTML_TWIG);
            }
        }

        return null;
    }

    private function getErrorMessage(string $new, string $current): string
    {
        return sprintf('Please use "%s" instead of "%s"', $new, $current);
    }
}
