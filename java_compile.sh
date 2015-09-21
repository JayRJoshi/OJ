dst=$1; shift
max_alloc_memory=$1; shift
time_limit=$1; shift
main_source=$1

javac -d $dst $main_source

cd $dst
main_source_name=$(basename $main_source)
main_class=${main_source_name%%.java}.class
if [ ! -f "$main_class" ]; then
	echo "Error: byte-compiled class file '$TMP.class' not found."
	exit 1
fi
echo finish
