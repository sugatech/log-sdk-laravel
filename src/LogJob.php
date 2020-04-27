<?php

namespace Log\SDK;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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

        $hash = sha1($this->type . '_' . implode(',', $keys));

        $version = DB::table('log_hashes')
            ->where('hash', $hash)
            ->value('version');

        if (is_null($version)) {
            $maxVersion = DB::table('log_hashes')
                ->where('type', $this->type)
                ->orderBy('id', 'desc')
                ->value('version');

            $version = ($maxVersion ?: 0) + 1;

            DB::table('log_hashes')->insert([
                'hash' => $hash,
                'type' => $this->type,
                'version' => $version,
                'keys' => json_encode($keys),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        app('log.client')->log($this->type, (string) $version, $data);
    }
}