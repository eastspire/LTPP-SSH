#!/bin/bash
# Build the LTPP-SSH single-file binary.
#
# Requires PHP 8.2 with phar.readonly=0 on the build host.
# The previous version embedded operator contact details in the
# comment header; that has been removed because the script ships in
# the public repository.
set -e
php webman build:bin 8.2
php webman build:bin 8.2
./sh/bin_up.sh
