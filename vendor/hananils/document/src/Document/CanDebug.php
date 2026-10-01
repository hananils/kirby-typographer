<?php

namespace Hananils\Document;

trait CanDebug
{
    public bool $debug = false;
    private array $log = [];

    private function log($name, $data = null)
    {
        if ($this->debug === false) {
            return;
        }

        $context = $this->simplifyNamespace(static::class);
        $backtrace = debug_backtrace(2, 3)[2];
        $class = $backtrace['class'] ?? '';
        $function = $backtrace['function'] ?? '';
        $type = $backtrace['type'] ?? '';
        $caller = $this->simplifyNamespace("$class$type$function");
        $indent = 0;

        if ($name === '__construct') {
            $signature = "new $context()";
        } else {
            $signature = $this->simplifyNamespace("$context->$name");
        }

        if ($last = array_last($this->log)) {
            $firstOccurence = array_find($this->log, function ($log) use (
                $caller
            ) {
                return $log['caller'] === $caller;
            });

            if ($firstOccurence) {
                $indent = $firstOccurence['indent'];
            } else {
                $indent = $last['indent'];

                if ($caller !== $last['caller']) {
                    $indent++;
                }
            }
        } else {
            dump($caller);
        }

        $this->log[] = [
            'signature' => $signature,
            'context' => $context,
            'method' => $name,
            'indent' => $indent,
            'data' => $data,
            'caller' => $caller,
            'time' => microtime(true)
        ];
    }

    private function report($html)
    {
        if ($this->debug === false) {
            return;
        }

        $callstack = [];
        $now = microtime(true);
        $times = array_column($this->log, 'time');
        $start = array_shift($times);
        array_push($times, $now);

        foreach (
            $this->log
            as $index =>
                [
                    'signature' => $signature,
                    'context' => $context,
                    'method' => $method,
                    'indent' => $indent,
                    'data' => $data,
                    'caller' => $caller,
                    'time' => $time
                ]
        ) {
            $virtual = '';
            if ($method === '__call') {
                $virtual = ': ' . $data['name'];
            }

            $duration = $this->timeDiff($time, $times[$index]);

            $callstack[] =
                str_repeat('·', $indent) .
                $signature .
                $virtual .
                " ($duration)";
        }

        dump($callstack);
        dump('Excecution time: ' . $this->timeDiff($start, $now));
        dump($this->log);
        // dump($html);
    }

    private function timeDiff($start, $end)
    {
        $diff = $end - $start;
        [$seconds, $milliseconds] = explode(
            '.',
            number_format($diff, 3, '.', '')
        );

        return intval($seconds) . 's ' . intval($milliseconds) . 'ms';
    }

    private function simplifyNamespace($classname)
    {
        return array_last(explode('\\', $classname));
    }
}
