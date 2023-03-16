<?php

namespace Hananils\Corrections;

use Hananils\Correction;
use DOMXPath;

class Lists extends Correction
{
    public $flow = 'block';

    public function apply()
    {
        $xpath = new DOMXPath($this->document);
        $lists = $xpath->query('//ul' . $this->createAncestorCondition());

        foreach ($lists as $list) {
            $content = '';
            foreach ($list->childNodes as $node) {
                $content .= trim($node->textContent);
            }

            if (
                strlen($content) / $list->childNodes->length <
                $this->option('list-density-limit', 75)
            ) {
                $classnames = [$list->getAttribute('class')];
                $classnames[] = $this->option(
                    'list-density-classname',
                    'is-dense'
                );

                $list->setAttribute('class', implode(' ', $classnames));
            }
        }
    }
}
