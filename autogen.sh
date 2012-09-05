#!/bin/bash

ACLOCAL_FLAGS="-I m4 $ACLOCAL_FLAGS"

autoreconf $@
