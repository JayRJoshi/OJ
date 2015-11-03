dst=$1; shift
#max_alloc_memory=$1; shift
#time_limit=$1; shift
main_source=$1

#javac -d $dst $main_source
cd $dst
echo from script $main_source

main_source_name=$(basename $main_source)
main_class=${main_source_name%%.java}
if [ -f "$main_class".class ]; then
	rm "$main_class".class
fi
javac $main_source 2> compile.err
echo $main_class
if [ ! -f "$main_class".class ]; then
	cat compile.err
	exit 1
fi
echo compiled...


exit 0
