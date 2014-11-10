<?php

function convertSizeToHumanReadable($size, $template = "%.2f %s") {
  $base = 1024;
  $unit = ["B", "Kb", "Mb", "Gb", "Tb", "Pb"];

  if ($size == 0)
  {
    return sprintf($template, $size, $unit[$size]);
  }
  else
  {
    $i = floor(log($size, $base));

    return sprintf($template, round($size / pow($base, $i), 2), $unit[$i]);
  }
}

