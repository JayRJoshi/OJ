dst=$1; shift
#max_alloc_memory=$1; shift
#time_limit=$1; shift
main_source=$1

javac -d $dst $main_source

cd $dst
main_source_name=$(basename $main_source)
main_class=${main_source_name%%.java}
if [ ! -f "$main_class".class ]; then
	echo "Error: byte-compiled class file '$main_source.class' not found."
	exit 1
fi
echo compiled...
echo "$dst$main_class"
MEMRESERVED=300000

#MEMLIMITJAVA=$((MEMLIMIT - MEMRESERVED))

#exec java -Xss8m -Xmx${MEMLIMITJAVA}k '$main_class'
exec java "$main_class"

exit 0
