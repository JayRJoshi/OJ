dst=$1; shift
max_alloc_memory=$1; shift
time_limit=$1; shift

gcc -x c -w -O2 -static -ansi -o "$DEST" "$@" -lm
exit $?
