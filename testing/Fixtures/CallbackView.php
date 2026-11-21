<?php

/*
 * A view whose content is whatever a callback outputs, so each test can make a view behave in its own way.
 */

namespace Programster\AbstractView\Testing\Fixtures;

use Programster\AbstractView\AbstractView;


final class CallbackView extends AbstractView
{
    private readonly \Closure $m_content;


    /**
     * @param \Closure(): void $content - outputs the view's content.
     */
    public function __construct(\Closure $content)
    {
        $this->m_content = $content;
    }


    protected function renderContent() : void
    {
        ($this->m_content)();
    }
}
