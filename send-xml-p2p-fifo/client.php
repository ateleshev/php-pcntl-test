<?php

$pid = getmypid();
$signal = 'SIGCHLD';
$parentId = (int) $argv[1];

if ($parentId > 0)
{
  $pipe = __DIR__ . DIRECTORY_SEPARATOR . "{$pid}.pipe";
  if (!file_exists($pipe))
  {
    posix_mkfifo($pipe, 0600);
  } 

  echo "[{$pid}] Send signal {$signal} to '{$parentId}' ... [";
  echo posix_kill($parentId, constant($signal)) ? "OK" : "Error";
  echo "]", PHP_EOL;

  $data = "<root><pid>{$pid}</pid><parent>{$parentId}</parent></root>";

  $f = fopen($pipe, 'w');
  echo "Start write to fifo: ... [" . (fwrite($f, $data) ? "OK" : "Error") . "]", PHP_EOL;

  fclose($f);
  unlink($pipe);
}

