#!/bin/bash
# Initialize the LTPP-SSH working tree.
#
# If you fork this repo on GitHub, update the remote URL below to point
# at your fork. The previous version of this file added three remotes
# (ltpp / github / origin) that pointed at internal LTPP infrastructure
# and the upsteam organization; that leaked internal hostnames and
# caused accidental cross-pushes, so it has been reduced to a single
# configurable remote.
#
# Usage:
#   git remote set-url origin git@github.com:<your-org>/LTPP-SSH.git
#   ./sh/init.sh     # verify the remote is set correctly
git remote -v
