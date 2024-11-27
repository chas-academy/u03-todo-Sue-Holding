#!/bin/bash

# Check if container is run through Compose
if [ "$compose" == true ]; then
    # Run the PHP development server
    php -S 0.0.0.0:80 -t ./src
else
    # Run the tests
    exec phpunit --testdox tests
fi