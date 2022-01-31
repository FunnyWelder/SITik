#!/bin/bash

commitRegex='^(\[[A-Z]+-[0-9]+\] [A-ZА-Я].+)$'
if ! grep -qE "$commitRegex" "$1"; then
    echo "Invalid message. Expected: [number of issue] message and first letter in uppercase"
    exit 1
fi