LOG2ERR=0
LOG2FILE=1

#log <logtype> <logfile> <msg>
log()
{

	local LOGTYPE=$1; shift
	local LOGFILE=$1; shift
	time_stamp=`date '+%d-%b-%Y %T'`
	if [ "$LOGTYPE" -eq $LOG2ERR ]; then
		echo "[$time_stamp] : \"$@\"" >&2
	fi
	if [ "$LOGTYPE" -eq $LOG2FILE ]; then
		if [ -f "$LOGFILE" ]; then
			echo "$time_stamp \"$@\"" >>$LOGFILE
		else
			echo "File $LOGFILE not found"
		fi
	fi
}

#error <msg>
error()
{
	set +e
	if [ "$@" ]; then
		log $LOG2ERR - "error: $@"
	else
		log $LOG2ERR - "unexpected error, aborting!"
	fi
	exit 127
}

#warning <msg>
warning ()
{
	log $LOG2ERR - "warning: $@"
}
