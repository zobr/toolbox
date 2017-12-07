<?php

namespace Zobr\Toolbox;

/**
 * MessageBag is used to collect all messages for convenient use inside
 * templates.
 */
class MessageBag {

    protected $messages = [];
    protected $ns = null;
    protected $empty = true;

    /**
     * Switches the namespace
     * @param  string|null $ns
     * @return self
     */
    public function for(string $ns = null) {
        $this->ns = $ns;
        return $this;
    }

    /**
     * Resets the namespace to default
     * @return self
     */
    public function reset() {
        $this->ns = null;
        return $this;
    }

    /**
     * Adds a message
     * @param string $message
     * @return self
     */
    public function add(string $message) {
        if (!isset($this->messages[$this->ns])) {
            $this->messages[$this->ns] = [];
        }
        $this->messages[$this->ns][] = $message;
        $this->empty = false;
        return $this;
    }

    /**
     * Check if this message bag has any message in any namespace
     * @return boolean
     */
    public function hasAny() {
        return !$this->empty;
    }

    /**
     * Check if this message bag has a message in current namespace
     * @return boolean
     */
    public function has($message) {
        foreach ($this->all() as $x) {
            if ($x === $message) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get first message in current namespace
     * @return string|null
     */
    public function first() {
        return $this->all()[0] ?? null;
    }

    /**
     * Returns all messages in current namespace
     * @return string[]
     */
    public function all() {
        if (!isset($this->messages[$this->ns])) {
            return [];
        }
        return $this->messages[$this->ns];
    }

}
