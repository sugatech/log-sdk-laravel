<?php

namespace Log\SDK;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $entries;

    /**
     * Create a new job instance.
     *
     * @param string $type
     * @param array $entries
     */
    public function __construct($type, $entries)
    {
        $this->type = $type;
        $this->entries = $entries;
    }

    public function handle()
    {
        $keys = array_column($this->entries, 'key');
        $data = array_column($this->entries, 'value');

        $version = sha1(implode(',', $keys));

        app('log.client')->log($this->type, $version, $data);
    }
}