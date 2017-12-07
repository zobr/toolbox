<?php

namespace Zobr\Toolbox\Perf;

class Profiler {

    private static $timings = [];
    private static $stdout;

    public static function flag($name) {
        self::$timings[] = [
            'name' => $name,
            'time' => microtime(true),
        ];
    }

    private static function print(string $str) {
        if (!self::$stdout) {
           self::$stdout = fopen('php://stdout', 'w');
        }
        fwrite(self::$stdout, $str);
    }

    private static function close() {
        if (self::$stdout) {
            fclose(self::$stdout);
        }
        self::$stdout = null;
    }

    public static function timings() {
        $initial = null;
        self::print(get_class() . "::timings()\n");
        foreach (self::$timings as $timing) {
            $name = $timing['name'];
            $time = $timing['time'];
            if (!$initial) {
                $initial = $time;
            }
            $diff = str_pad(number_format($time - $initial, 6), 12, ' ', STR_PAD_LEFT);
            self::print($diff . ' - ' . $name . "\n");
        }
        self::close();
    }

}
