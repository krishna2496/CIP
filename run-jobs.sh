#!/bin/bash
while true;

do php artisan schedule:run > /dev/stdout 2>/dev/stderr ;
done
