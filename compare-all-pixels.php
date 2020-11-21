<?php

$frown = imagecreatefrompng('frown.png');
$frownW = imagesx($frown);
$frownH = imagesy($frown);
$map = _get_map($frown, 0, 0, $frownW, $frownH, $frownW, $frownH);
if (!count($map)) {
  die("Error map count is 0" . PHP_EOL);
}

$map0 = $map[0];

#$image = imagecreatefrompng('icons.png');
$image = imagecreatefrompng('noicons.png');

$w = imagesx($image);
$h = imagesy($image);

$maxX = $w - $frownW;
$maxY = $h - $frownH;
#$maxX = 1500;
#$maxY = 400;
for ($x = 0; $x < $maxX; $x++) {
  for ($y = 0; $y < $maxY; $y++) {
    $rgb = imagecolorat($image, $x, $y);
    if ($rgb == $map0) {
      #print "hit map0 at $x, $y" . PHP_EOL;
      $chk = _get_map($image, $x, $y, $frownW, $frownH, $w, $h);
      $compare = _compare_maps($map, $chk);
      if ($compare) {
        die("Found frown at $x, $y, $compare" . PHP_EOL);
      }
    }
  }
}

function _get_map($image, $x, $y, $w, $h, $maxW, $maxH) {
  $map = [];
  $w = $x + $w;
  $h = $y + $h;
  if ($w > $maxW) $w = $maxW;
  if ($h > $maxH) $h = $maxH;
  for ($i = $x; $i < $w; $i++) {
    for ($j = $y; $j < $h; $j++) {
      #print "$i, $j\n";
      $map[] = imagecolorat($image, $i, $j);
    }
  }
  return $map;
}

function _compare_maps($map1, $map2) {
  $threshold = 0.85;
  // let's try to diff first for exact match
  $diff = array_diff($map1, $map2);
  if (!count($diff)) {
    return 1;
  }
  $totalPixels = 0;
  $totalPixelDiffs = 0;
  #print "count(map1) = " . count($map1) . PHP_EOL;
  #print "count(map2) = " . count($map1) . PHP_EOL;
  for ($i = 0; $i < count($map1); $i++) {
    $totalPixels += $map1[$i];
    $pixel1 = $map1[$i];
    $pixel2 = $map2[$i];
    $totalPixelDiffs += abs($pixel1 - $pixel2);
    $delta = 1 - ($totalPixelDiffs / $totalPixels);
    if ($delta < $threshold) return 0;
    #print "$delta ";
  }
  return $delta;
}
