<?php

$frown = imagecreatefrompng('frown.png');
$frownW = imagesx($frown);
$frownH = imagesy($frown);
$map = _get_map($frown, 0, 0, $frownW, $frownH, $frownW, $frownH);
if (!$map['colorsTotal']) {
  die("Error map count is 0" . PHP_EOL);
}

$map0 = $map['firstColor'];

#$image = imagecreatefrompng('noicons.jpg');
$image = imagecreatefrompng('icons.png');

$w = imagesx($image);
$h = imagesy($image);

$maxX = $w - $frownW;
$maxY = $h - $frownH;
#$maxX = 1500;
#$maxY = 400;
$highestThreshold = 0;
$highestX = 0;
$highestY = 0;
$threshold = 0.99;
$step = 6;
for ($x = 0; $x < $maxX; $x += $step) {
  for ($y = 0; $y < $maxY; $y += $step) {
    $rgb = imagecolorat($image, $x, $y);
    if ($rgb == $map0) {
      #print "hit map0 at $x, $y" . PHP_EOL;
      $chk = _get_map($image, $x, $y, $frownW, $frownH, $w, $h);
      $compare = _compare_maps($map, $chk);
      if ($compare > $highestThreshold) {
        $highestThreshold = $compare;
        $highestX = $x;
        $highestY = $y;
      }
      if ($compare > $threshold) {
        die("Found frown at $x, $y, $compare" . PHP_EOL);
      }
    }
  }
}
print "highestThreshold = $highestThreshold, $highestX, $highestY" . PHP_EOL;

function _get_map($image, $x, $y, $w, $h, $maxW, $maxH) {
  $firstColor = null;
  $colors = [];
  $colorsTotal = 0;
  #$map = [];
  $w = $x + $w;
  $h = $y + $h;
  if ($w > $maxW) $w = $maxW;
  if ($h > $maxH) $h = $maxH;
  for ($i = $x; $i < $w; $i++) {
    for ($j = $y; $j < $h; $j++) {
      #print "$i, $j\n";
      $rgb = imagecolorat($image, $i, $j);
      if (!isset($colors[$rgb])) $colors[$rgb] = 0;
      $colors[$rgb] += 1;
      $colorsTotal += $rgb;
      if (is_null($firstColor)) $firstColor = $rgb;
      #$map[] = $rgb;
    }
  }
  return compact('firstColor', 'colors', 'colorsTotal');
  #return compact('map', 'colors', 'colorsTotal');
}

function _compare_maps($map1, $map2) {
  $threshold = 0.85;
  if ($map1['colorsTotal'] == $map2['colorsTotal']) return 1;
  $colorsTotal1 = $map1['colorsTotal'];
  $colorsTotal2 = $map2['colorsTotal'];
  $diff = $colorsTotal2 / $colorsTotal1;
  return $diff;
  if ($diff > $threshold) return $threshold;
  return 0;
}
