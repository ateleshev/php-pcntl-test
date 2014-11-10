<?php
// Test for maximum throughput when exchanging data via FIFO 

$executionTime = 1; // by default

if (isset($argv[1]))
{
  $executionTime = max($executionTime, intval($argv[1]));
}

$filepath = __DIR__.'/'.getmypid();

$created = posix_mkfifo($filepath, 0777);
if (!$created)
{
  echo "Unable to created FIFO $filepath\n";
  exit();
}

$pid = pcntl_fork();

if ($pid > 0)
{
  // parent

  $fp = fopen($filepath, "r");

  if ($fp)
  {
    while (fread($fp, 8192));
    fclose($fp);
  }

  unlink($filepath);
}
else if ($pid == 0)
{
  // child

  echo "Child started\n";

  $str = str_pad('6p92uor134', 8192, '0');
  $writed = 0;

  $fp = fopen($filepath, "w");

  if ($fp)
  {
    $startedAt = microtime(true);
    while ((microtime(true) - $startedAt) < $executionTime)
    {
      $writed += fwrite($fp, $str);
    }
    if ($fp) fclose($fp);
  }

  $perSec = round($writed / $executionTime);
  echo "Written $writed bytes ($perSec per second)\n";

  exit(0);
}
