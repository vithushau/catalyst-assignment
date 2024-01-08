<?php

$number = 1;

while ($number <= 100) {
    $output = '';
    if ($number % 3 == 0) {
        $output .= 'foo';
    }
    if ($number % 5 == 0) {
        $output .= 'bar';
    }
    echo ($output !== '') ? $output : $number;
    if ($number < 100) {
        echo ', ';
    }
    $number++;
}

