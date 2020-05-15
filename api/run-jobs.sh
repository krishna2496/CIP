#!/bin/bash

php artisan schedule:run > /dev/stdout 2>/dev/stderr ;
