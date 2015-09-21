LOG2ERR=0
LOG2FILE=1

log()
{
	local LOGTYPE=$1; shift
	local LOGFILE=$1; shift
	time_stamp=`date '+%d-%b-%Y %T'`
	if [ "$LOGTYPE" -eq 0 ]; then
		echo "[$time_stamp] : \"$@\"" >&2
	fi
	if [ "$LOGTYPE" -eq 1 ]; then
		if [ "$LOGFILE" ]; then
			echo "$time_stamp \"$@\"" >>$LOGFILE
		fi
	fi
}

error()
{
	set +e
	trap - EXIT
	if [ "$@" ]; then
		log "error: $@"
	else
		log "unexpected error, aborting!"
	fi
	exit 127
}

warning ()
{
	log "warning: $@"
}
