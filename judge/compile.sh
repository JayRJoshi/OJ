# compile.sh <compile_script> <dst> <max_alloc_memory> <time_limit> <main_source> <other source_files>
# compile_script    :- full path to compile_script
# dst		    :- Where compiled files will be stored
# max_alloc_memory  :- max memory to allocate for compilation
# time_limit        :- time limit for script
# main_source       :- One which will execute

compile_script=$1; shift
dst=$1; shift
max_alloc_memory=$1; shift
time_limit=$1; shift

current_dir=`pwd`
cd $dst

#for source_file in "$@" ; do
#	[ -r "$source_file"  ] || echo "source file not found: $source_file" && exit 1
#	chmod a+r "$source_file"
#done
echo code_evaluator
su - code_evaluator -c "$compile_script $dst $max_alloc_memory $time_limit \"$@\""
