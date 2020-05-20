#!/bin/bash
SCRIPT_DIR=$(cd $(dirname $0); pwd)
. ${SCRIPT_DIR}/dev.sh run --rm $@