#!/bin/bash
# Push the compiled single-file binary to the deployment host.
#
# Required environment:
#   DEPLOY_HOST         target host, e.g. deploy@example.com
#   DEPLOY_PORT         ssh port (defaults to 22)
#   DEPLOY_SSH_KEY      absolute path to the SSH private key on the
#                       operator's machine. Must be readable only by
#                       the current user (chmod 600).
#
# The previous version of this script hardcoded the operator's local
# Windows user directory and the on-disk location of the deploy key,
# which leaks the operator's home directory layout to anyone reading
# the public repository. It now requires the deploy key path to be
# passed in via DEPLOY_SSH_KEY and refuses to run if it is unset or
# world-readable.
set -euo pipefail

: "${DEPLOY_HOST:?DEPLOY_HOST is required, e.g. deploy@example.com}"
DEPLOY_PORT="${DEPLOY_PORT:-22}"
: "${DEPLOY_SSH_KEY:?DEPLOY_SSH_KEY is required, e.g. /home/you/.ssh/id_ed25519}"

if [ ! -f "${DEPLOY_SSH_KEY}" ]; then
    echo "DEPLOY_SSH_KEY points at a non-existent file: ${DEPLOY_SSH_KEY}" >&2
    exit 1
fi

KEY_PERMS=$(stat -c '%a' "${DEPLOY_SSH_KEY}" 2>/dev/null || stat -f '%Lp' "${DEPLOY_SSH_KEY}")
case "${KEY_PERMS}" in
    600|400) ;;
    *) echo "Refusing to use ${DEPLOY_SSH_KEY} with permissions ${KEY_PERMS}; expected 600 or 400." >&2; exit 1 ;;
esac

scp -P "${DEPLOY_PORT}" -rp -i "${DEPLOY_SSH_KEY}" ./build/LTPP-SSH "${DEPLOY_HOST}:/tmp/"
echo "Press Enter to continue..."
read -n 1
