<?php

namespace HangerSnippet\View\Helper;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\View\Helper\AbstractHelper;
use HangerSnippet\Exception\InvalidArgumentException;

/**
 * Class SnippetHelper
 * @author Lorenzo Fontana <fontanalorenzo@me.com>
 * @author Leonardo Grasso <me@leonardograsso.com>
 */
class SnippetHelper extends AbstractHelper
{

    /**
     * Snippets
     * @var array
     */
    protected $snippets = [];

    /**
     * Enabled Snippets
     * @var array
     */
    protected $enabledSnippets = [];


    /**
     * Set Enabled
     * @param string $name The Snippet name
     * @return \HangerSnippet\View\Helper\SnippetHelper
     */
    public function setEnabled($name)
    {
        $this->checkIfSnippetExists($name);
        $this->enabledSnippets[$name] = true;
        return $this;
    }


    /**
     * Set Disabled
     * @param string $name The Snippet name
     * @return \HangerSnippet\View\Helper\SnippetHelper
     */
    public function setDisabled($name)
    {
        $this->checkIfSnippetExists($name);
        $this->enabledSnippets[$name] = false;
        return $this;
    }

    /**
     * Set Enable All
     * @return $this
     */
    public function setEnableAll()
    {
        foreach ($this->snippets as $name => $config) {
            $this->setEnabled($name);
        }
        return $this;
    }

    /**
     * Set Disable All
     * @return $this
     */
    public function setDisableAll()
    {
        foreach ($this->snippets as $name => $config) {
            $this->setDisabled($name);
        }
        return $this;
    }

    /**
     * Append Snippet
     * @param string      $name     The Snippet name
     * @param string      $template The Snippet template
     * @param array       $values
     * @param string|null $placement
     * @param bool        $enabled
     * @return \HangerSnippet\View\Helper\SnippetHelper
     */
    public function appendSnippet($name, $template, array $values = [], $placement = null, $enabled = true)
    {
        $this->snippets[$name] = [
            'placement' => $placement,
            'template'  => $template,
            'values'    => $values,
        ];

        $this->setEnabled($name, $enabled);
        return $this;
    }

    /**
     * Render a single snippet
     *
     * @param string $name The snippet name
     * @throws InvalidArgumentException
     * @return string
     */
    public function renderSnippet($name)
    {
        if (!isset($this->snippets[$name])) {
            throw new InvalidArgumentException("Cannot find a snippet with name '{$name}'");
        }

        return $this->getView()->render($this->snippets[$name]['template'], $this->snippets[$name]['values']);
    }

    /**
     * Render
     * @param string $placement
     * @throws InvalidArgumentException
     * @return string
     */
    public function render($placement = null)
    {
        $snippets = $this->enabledSnippets;

        $pieces = [];
        foreach ($snippets as $name => $enabled) {
            if ($enabled && $this->snippets[$name]['placement'] === $placement) {
                $pieces[] = $this->renderSnippet($name);
            }
        }

        return implode(PHP_EOL, $pieces);
    }

    /**
     * To String
     * @return string
     */
    public function toString()
    {
        return $this->render();
    }

    /**
     * To String
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * View Helper Invoke
     * @return $this
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * Get Snippets
     * @return array
     */
    public function getSnippets()
    {
        return $this->snippets;
    }

    /**
     * Get Enabled Snippets
     * @return array
     */
    public function getEnabledSnippets()
    {
        return $this->enabledSnippets;
    }

    /**
     * Check if snippet exists
     * @param $name
     * @throws \HangerSnippet\Exception\InvalidArgumentException
     */
    protected function checkIfSnippetExists($name)
    {
        if (!isset($this->snippets[$name])) {
            throw new InvalidArgumentException("Cannot find a snippet with name '{$name}'");
        }
    }
}
