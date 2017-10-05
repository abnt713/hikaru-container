<?php
namespace AlisonBnt\Hikaru;

use Psr\Container\ContainerInterface;

class HikaruContainer implements ContainerInterface
{
    private $entries;

    public function __construct($entries = array())
    {
        $this->entries = $entries;
    }

    public function get($id)
    {
        if (!$this->has($id)) {
            throw new EntryNotFoundException("Entry {$id} not found");
        }

        $entry = $this->entries[$id];
        $result = $entry;
        if (is_callable($entry)) {
            $result = call_user_func($entry, $this);
            $this->entries[$id] = $result;
        }

        return $result;
    }

    public function has($id)
    {
        return isset($this->entries[$id]);
    }
}
