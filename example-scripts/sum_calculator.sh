#!/bin/bash

# this shell file just for tests

# Check if at least one argument is provided
if [ $# -eq 0 ]; then
    echo '{"error": "At least one numeric argument is required."}'
    exit 1
fi

# Initialize sum
sum=0

# Iterate over arguments and check if they are numbers
for arg in "$@"; do
    if ! [[ "$arg" =~ ^-?[0-9]+([.][0-9]+)?$ ]]; then
        echo '{"error": "All arguments must be numeric."}'
        exit 1
    fi
    sum=$(echo "$sum + $arg" | bc)
done

# Output the sum in JSON format
echo "$sum"
