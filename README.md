php-pcntl-test
==============

Test: process control in PHP

Test 1: Pipe throughput

Intel(R) Xeon(R) CPU L5420 @ 2.50GHz
------------------------------------
php send-xml-c2p-fifo/throughput.php 15
 -  Written 34.66 Gb (2.31 Gb/sec)
 -  Written 34.79 Gb (2.32 Gb/sec)
 -  Written 34.60 Gb (2.31 Gb/sec)

Intel(R) Core(TM) i5-3330 CPU @ 3.00GHz
---------------------------------------
php send-xml-c2p-fifo/throughput.php 15
 -  Written 69.80 Gb (4.65 Gb/sec)
 -  Written 71.78 Gb (4.79 Gb/sec)
 -  Written 69.43 Gb (4.63 Gb/sec)
