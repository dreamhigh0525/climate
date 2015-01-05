<?php

namespace League\CLImate\Decorator;

class Parser
{
    /**
     * An array of the currently applied codes
     *
     * @var array $current;
     */
    protected $current;

    /**
     * All of the possible styles available
     *
     * @var array $all
     */
    protected $all;

    /**
     * An array of the tags that should be searched for
     * and their corresponding replacements
     *
     * @var array $tags
     */
    public $tags  = [];

    public function __construct(array $current, array $all)
    {
        $this->current = $current;
        $this->all     = $all;

        $this->buildTags();
    }

    /**
     * Wrap the string in the current style
     *
     * @param  string $str
     * @return string
     */
    public function apply($str)
    {
        return $this->start() . $this->parse($str) . $this->end();
    }

    /**
     * Get the string that begins the style
     *
     * @return string
     */
    protected function start($codes = null)
    {
        $codes = $codes ?: $this->currentCode();
        $codes = $this->codeStr($codes);

        return $this->wrapCodes($codes);
    }

    /**
     * Get the string that ends the style
     *
     * @return string
     */
    protected function end($codes = null)
    {
        if (empty($codes)) {
            $codes = [0];
        } else {
            if (!is_array($codes)) {
                $codes = [$codes];
            }
            // Reset everything back to normal up front
            array_unshift($codes, 0);
        }

        return $this->wrapCodes($this->codeStr($codes));
    }

    /**
     * Wrap the code string in the full escaped sequence
     *
     * @param  string $codes
     * @return string
     */
    protected function wrapCodes($codes)
    {
        return "\e[{$codes}m";
    }

    /**
     * Get the regular expression that can be used to parse the string for tags
     *
     * @return string
     */

    protected function getRegexForTags()
    {
        return '(<(?:(?:(?:\\\)*\/)*(?:' . implode('|', array_keys($this->all)) . '))>)';
    }

    /**
     * Parse the string for tags and replace them with their codes
     *
     * @param  string $str
     * @return string
     */
    protected function parse($str)
    {
        $count = preg_match_all($this->getRegexForTags(), $str, $matches);

        // If we didn't find anything, return the string right back
        if (!$count || !is_array($matches)) {
            return $str;
        }

        // All we want is the array of actual strings matched
        $matches = reset($matches);

        return $this->parseTags($str, $matches);
    }

    /**
     * Parse the string for tags and remove them
     *
     * @param  string $str
     * @return string
     */

    public function ignore($str)
    {
        return preg_replace($this->getRegexForTags(), "", $str);
    }

    /**
     * Parse the given string for the tags and replace them with the appropriate codes
     *
     * @param string $str
     * @param array $tags
     */
    protected function parseTags($str, $tags)
    {
        // Let's keep a history of styles applied
        $history = ($this->currentCode()) ? [$this->currentCode()] : [];

        foreach ($tags as $tag) {
            $str = $this->replaceTag($str, $tag, $history);
        }

        return $str;
    }

    /**
     * Replace the tag in the str
     *
     * @param string $str
     * @param string $tag
     * @param array $history
     * @return string
     */
    protected function replaceTag($str, $tag, &$history)
    {
        // We will be replacing tags one at a time, can't pass this by reference
        $replace_count = 1;

        if (strstr($tag, '/')) {
            // We are closing out the tag, pop off the last element and get the codes that are left
            array_pop($history);
            $replace = $this->end($history);
        } else {
            // We are starting a new tag, add it onto the history and replace with correct color code
            $history[] = $this->tags[$tag];
            $replace = $this->start($this->tags[$tag]);
        }

        return str_replace($tag, $replace, $str, $replace_count);
    }

    /**
     * Build the search and replace for all of the various style tags
     */
    protected function buildTags()
    {
        $tags       = $this->all;
        $this->tags = [];

        foreach ($tags as $tag => $color) {
            $this->tags["<{$tag}>"]    = $color;
            $this->tags["</{$tag}>"]   = $color;
            $this->tags["<\\/{$tag}>"] = $color;
        }
    }

    /**
     * Stringify the codes
     *
     * @param  mixed  $codes
     * @return string
     */
    protected function codeStr($codes)
    {
        // If we get something that is already a code string, just pass it back
        if (!is_array($codes) && strstr($codes, ';')) {
            return $codes;
        }

        if (!is_array($codes)) {
            $codes = [$codes];
        }

        // Sort for the sake of consistency and testability
        sort($codes);

        return implode(';', $codes);
    }

    /**
     * Retrieve the current style code
     *
     * @return string
     */
    protected function currentCode()
    {
        return $this->codeStr($this->current);
    }
}
