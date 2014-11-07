<?php

$pid = getmypid();
$signal = 'SIGCHLD';

echo "PID: {$pid}", PHP_EOL;

pcntl_signal(constant($signal), function($signo) use ($signal) {
  echo "Signal handler called: {$signal} -> {$signo}", PHP_EOL;
});

echo "Dispatching...", PHP_EOL;

while (1)
{
  pcntl_signal_dispatch();

  $siginfo = [];
  pcntl_sigprocmask(SIG_BLOCK, [constant($signal)]);
  $signo = pcntl_sigtimedwait([constant($signal)], $siginfo, 3);

  if ($signo == constant($signal) && isset($siginfo['pid']))
  {
    $childId = $siginfo['pid'];
    echo "Child request: {$childId}", PHP_EOL;

    $pipe = __DIR__ . DIRECTORY_SEPARATOR . "{$childId}.pipe";
    if (file_exists($pipe))
    {
      $f = fopen($pipe, 'r');

      echo "Received: ", stream_get_contents($f), PHP_EOL;

      fclose($f);
    }
    else
    {
      echo "Fifo not foud: {$pipe}", PHP_EOL;
    }
  }
  else if ($signo != -1)
  {
    echo "SigNo: {$signo}", PHP_EOL;
    var_dump($siginfo);
  }

  usleep(2000);
}

