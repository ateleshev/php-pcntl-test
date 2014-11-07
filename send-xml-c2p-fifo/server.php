<?php

$signal = 'SIGCHLD';
$children = [];
$parentId = getmypid();

echo "PARENT | PID: {$parentId}" . PHP_EOL;

$dir = implode(DIRECTORY_SEPARATOR, [__DIR__, 'pipes', $parentId]);;

if (!file_exists($dir))
{
  if (!mkdir($dir, 0777, true))
  {
    die("Failed to create: '{$dir}'");
  }
}

if (!is_dir($dir))
{
  die("Isn't dir: '{$dir}'");
}

pcntl_signal(constant($signal), function($signo) use (&$parentId, &$signal, &$dir, &$children) {
  if (count($children))
  {
    echo "PARENT | [{$parentId}] Signal handler called: {$signal} -> {$signo}; Childs = ";
    var_export($children);
    echo PHP_EOL;

    if ($signo == constant($signal))
    {
      echo "PARENT | [{$parentId}] Check pipes: " . PHP_EOL;

      if ($handle = opendir($dir))
      {
        while (false !== ($entry = readdir($handle)))
        {
          if ($entry != "." && $entry != "..")
          {
            echo "PARENT | [{$parentId}] Found child '{$entry}': ". PHP_EOL;
            if (isset($children[$entry]))
            {
              $pipe = $dir . DIRECTORY_SEPARATOR . "{$entry}";
              if ($f = fopen($pipe, 'r'))
              {
                echo "PARENT | [{$parentId}] Received from '{$pipe}': " . stream_get_contents($f) . PHP_EOL;
                unset($children[$entry]);
                fclose($f);
              }
            }
          }
        }
        closedir($handle);
      }
    }
  }
  else
  {
    echo "PARENT | [{$parentId}] Children not found " . PHP_EOL;
  }
});

echo "PARENT | [{$parentId}] Dispatching..." . PHP_EOL;

for ($i = 0; $i < 10; $i++)
{
  $pid = pcntl_fork();
  if ($pid == -1)
  {
    die('could not fork');
  }
  else if ($pid)
  {
    $children[$pid] = $pid;
    echo "PARENT | [{$parentId}] Forck child: {$pid}" . PHP_EOL;
  }
  else
  {
    $childPid = getmypid();
    $pipe = $dir . DIRECTORY_SEPARATOR . "{$childPid}";
    if (!file_exists($pipe))
    {
      posix_mkfifo($pipe, 0600);
    } 

    $info  = "CHILD | [{$childPid}] Send signal {$signal} to '{$parentId}' ... [";
    $info .= posix_kill($parentId, constant($signal)) ? "OK" : "Error";
    $info .= "]" . PHP_EOL;

    echo $info;

    $data = "<root><pid>{$childPid}</pid><parent>{$parentId}</parent></root>";

    echo "CHILD | [{$childPid}] Start write to fifo '{$pipe}'" . PHP_EOL;
    $f = fopen($pipe, 'w');
    echo "CHILD | [{$childPid}] Writing ... [" . (fwrite($f, $data) ? "OK" : "Error") . "]" . PHP_EOL;

    fclose($f);
    unlink($pipe);

    exit(0);
  }
}

while (count($children))
{
  pcntl_signal_dispatch();

  usleep(2000);
}

echo "PARENT | [{$parentId}] is complete" . PHP_EOL;

