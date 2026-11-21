<?php

namespace Programster\AbstractView;


abstract class AbstractView implements \Stringable
{
    /**
     * Render the view to a string. If the view throws, everything it had output so far is discarded, so a
     * half-rendered view can't end up in whatever is output next (such as an error page).
     *
     * @return string
     *
     * @throws \LogicException - if the view closed the output buffer that this method opened for it.
     */
    public function render()
    {
        $startingLevel = ob_get_level();
        ob_start();

        try
        {
            $this->renderContent();

            if (ob_get_level() <= $startingLevel)
            {
                throw new \LogicException(static::class . '::renderContent() closed the output buffer it renders to.');
            }

            // Keep the output of any buffer the view opened and forgot to close.
            while (ob_get_level() > $startingLevel + 1 && ob_end_flush())
            {
                // Each pass flushes one buffer into the one below it.
            }

            $content = (string)ob_get_contents();
        }
        finally
        {
            while (ob_get_level() > $startingLevel && ob_end_clean())
            {
                // Each pass discards one buffer.
            }
        }

        return $content;
    }


    public function __toString() : string { return $this->render(); }


    /**
     * Output the view's content.
     * @return void
     */
    abstract protected function renderContent();
}
