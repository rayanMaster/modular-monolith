#!/bin/bash

# Ensure Git case sensitivity is enabled
git config core.ignorecase false

# Find and rename files with "WorkSite" to "Worksite"
for file in $(git ls-files | grep 'WorkSite'); do
    # Rename the file to the correct case
    new_name=$(echo "$file" | sed 's/WorkSite/Worksite/g')
    echo "Renaming $file to $new_name"
    git mv "$file" "$new_name"
done
