#!/bin/sh
curl -I -s "$1" | grep -i '^location:' | cut -d ' ' -f2