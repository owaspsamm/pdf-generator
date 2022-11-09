#!/bin/bash

echo "Running DB Setup script"
sh ./scripts/setup_database.sh
echo "Finished executing DB Setup script"

echo "Generating PDF"
php bin/console app:save-pdf

