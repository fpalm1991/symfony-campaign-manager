<?php

namespace App\Service;

use League\CommonMark\CommonMarkConverter;

class MarkdownRenderer
{

    private CommonMarkConverter $converter;

    public function __construct()
    {
        $this->converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'renderer' => [
                'soft_break' => "<br>" . PHP_EOL,
            ],
        ]);
    }

    public function toHtml(?string $markdown): ?string
    {
        return (string)$this->converter->convert($markdown ?? '');
    }

}
