#include <sys/types.h>
#include <sys/wait.h>
#include <sys/param.h>
#include <sys/select.h>
#include <sys/stat.h>
#include <sys/time.h>
#include <sys/times.h>
#include <sys/resource.h>
#include <errno.h>
#include <fcntl.h>
#include <signal.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <stdarg.h>
#include <stdio.h>
#include <getopt.h>
#include <pwd.h>
#include <grp.h>
#include <time.h>
#include <math.h>
#include <limits.h>

#include <inttypes.h>
#include <libcgroup.h>
#include <sched.h>
#include <sys/sysinfo.h>
void setrestrictions();
void terminate();
void alarm_handler(int);
int timelimit=10;
int memsize=50000;
int filesize=1;
int nproc=RLIM_INFINITY;
rlim_t cputime_limit;
int child_pid;
int walllimit_reached;
const int hard_timelimit = 2;
struct itimerval itimer;
double walltime[2];
double tmpd;
int status;
int cpulimit_reached;
struct tms startticks, endticks;
void setrestrictions()
{
	printf("inside");
	char *path;
	char  cwd[PATH_MAX+1];

	struct rlimit lim;

	/* Clear environment to prevent all kinds of security holes, save PATH 
	path = getenv("PATH");
	environ[0] = NULL;
	/* FIXME: Clean path before setting it again? 
	if ( path!=NULL ) setenv("PATH",path,1);*/

	/* Set resource limits: must be root to raise hard limits.
	   Note that limits can thus be raised from the systems defaults! */

	/* First define shorthand macro function */

#define setlim(type) \
	if ( setrlimit(RLIMIT_ ## type, &lim)!=0 ) { \
		printf("problem setting limit ");\
	}\
	else printf("success");

	//if ( use_cputime ) {
		/* The CPU-time resource limit can only be specified in
		   seconds, so round up: we can measure actual CPU time used
		   more accurately. Also set the real hard limit one second
		   higher: at the soft limit the kernel will send SIGXCPU at
		   the hard limit a SIGKILL. The SIGXCPU can be caught, but is
		   not by default and gives us a reliable way to detect if the
		   CPU-time limit was reached. */
		cputime_limit = (rlim_t)(timelimit);
		lim.rlim_cur = cputime_limit;
		lim.rlim_max = cputime_limit+1;
		setlim(CPU);
	//}

	/* Memory limits may be handled by cgroups now */
//#ifndef USE_CGROUPS
/*if ( memsize!=RLIM_INFINITY ) {
		//verbose("setting memory limits to %d bytes",(int)memsize);
		lim.rlim_cur = lim.rlim_max = memsize;
		setlim(AS);
		setlim(DATA);
	}
/*#else
	/* Memory limits should be unlimited when using cgroups 
	lim.rlim_cur = lim.rlim_max = RLIM_INFINITY;
	setlim(AS);
	setlim(DATA);
#endif
*/

	/* Always set the stack size to be unlimited. */
	lim.rlim_cur = lim.rlim_max = RLIM_INFINITY;
	setlim(STACK);

	if ( filesize!=RLIM_INFINITY ) {
		//verbose("setting filesize limit to %d bytes",(int)filesize);
		lim.rlim_cur = lim.rlim_max = filesize;
		setlim(FSIZE);
	}

	/*if ( nproc!=RLIM_INFINITY ) {
		//verbose("setting process limit to %d",(int)nproc);
		lim.rlim_cur = lim.rlim_max = nproc;
		setlim(NPROC);
	}*/

#undef setlim

	/*if ( no_coredump ) {
		verbose("disabling core dumps");
		lim.rlim_cur = lim.rlim_max = 0;
		if ( setrlimit(RLIMIT_CORE,&lim)!=0 ) error(errno,"disabling core dumps");
	}*/

//#ifdef USE_CGROUPS
	/* Put the child process in the cgroup */
//	cgroup_attach();
//#endif

	/* Run the command in a separate process group so that the command
	   and all its children can be killed off with one signal. */
	if ( setsid()==-1 ) printf("problem setting sid");
}
	/* Set root-directory and change directory to there. */
	/*if ( use_root ) {
		/* Small security issue: when running setuid-root, people can find
		   out which directories exist from error message. */
		//if ( chdir(rootdir)!=0 ) error(errno,"cannot chdir to `%s'",rootdir);

		/* Get absolute pathname of rootdir, by reading it. */
		//if ( getcwd(cwd,PATH_MAX)==NULL ) error(errno,"cannot get directory");
		//if ( cwd[strlen(cwd)-1]!='/' ) strcat(cwd,"/");

		/* Canonicalize CHROOT_PREFIX. */
	//	if ( (path = (char *) malloc(PATH_MAX+1))==NULL ) {
	//		error(errno,"allocating memory");
	//	}
	//	if ( realpath(CHROOT_PREFIX,path)==NULL ) {
	//		error(errno,"cannot canonicalize path '%s'",CHROOT_PREFIX);
	//	}

		/* Check that we are within prescribed path. */
	//	if ( strncmp(cwd,path,strlen(path))!=0 ) {
	//		error(0,"invalid root: must be within `%s'",path);
	//	}
	//	free(path);

//		if ( chroot(".")!=0 ) error(errno,"cannot change root to `%s'",cwd);
		/* Just to make sure and satisfy Coverity scan: */
	//	if ( chdir("/")!=0 ) error(errno,"cannot chdir to `/' in chroot");
	//	verbose("using root-directory `%s'",cwd);
	//}

	/* Set group-id (must be root for this, so before setting user). */
	/*if ( use_group ) {
		if ( setgid(rungid) ) error(errno,"cannot set group ID to `%d'",rungid);
		//verbose("using group ID `%d'",rungid);
	}
	/* Set user-id (must be root for this). */
	/*if ( use_user ) {
		if ( setuid(runuid) ) error(errno,"cannot set user ID to `%d'",runuid);
		//verbose("using user ID `%d' for command",runuid);
	} else {
		/* Permanently reset effective uid to real uid, to prevent
		   child command from having root privileges.
		   Note that this means that the child runs as the same user
		   as the watchdog process and can thus manipulate it, e.g. by
		   sending SIGSTOP/SIGCONT! */
		//if ( setuid(getuid()) ) //error(errno,"cannot reset real user ID");
		//verbose("reset user ID to `%d' for command",getuid());
	//}
	//if ( geteuid()==0 || getuid()==0 ) {
	//	error(0,"root privileges not dropped. Do not run judgedaemon as root.");
	//}
int main(){
	char *a;
	//int child_pid;
	char *run[]={"sh","-c","./fire.o",NULL};
	char *path;
	char  cwd[PATH_MAX+1];
	sigset_t emptymask,sigmask;
	struct rlimit lim;
	struct sigaction sigact;
	int exitcode;
	fflush(stdout);
	switch(child_pid=fork()){
		case -1:printf("not created");
		case 0:
			printf("case 0");
			printf("inside");
			setrestrictions();
			fflush(stdout);
			
			execvp("/bin/sh",run);
			
		//system("./fire.o");
		default: /* become watchdog */
	//if (sigaction(SIGALRM, NULL, NULL) == -1)
        //err(1, NULL);

    // install an alarm handler for SIGALRM
    //signal(SIGALRM, alarm_handler);
    signal(SIGTERM, alarm_handler);
    // install an alarm to be fired after TIME_LIMIT
    alarm(0);

    return 0;
}
}

void alarm_handler(int sig)
{const struct timespec killdelay = { 0, 100000000L };
	struct sigaction sigact;
    printf("%s\n", "Seems you crossed time limit!");
	if ( kill(-child_pid,SIGKILL)!=0 && errno!=ESRCH ) {
		//error(errno,"sending SIGKILL to command");
	}

	/* Wait another while to make sure the process is killed by now. */
	nanosleep(&killdelay,NULL);

	}
void terminate(int sig)
{
	const struct timespec killdelay = { 0, 100000000L };
	struct sigaction sigact;

	/* Reset signal handlers to default */
	sigact.sa_handler = SIG_DFL;
	sigact.sa_flags = 0;
	if ( sigemptyset(&sigact.sa_mask)!=0 ) {
		//warning("could not initialize signal mask");
	}
	if ( sigaction(SIGTERM,&sigact,NULL)!=0 ) {
		//warning("could not restore signal handler");
	}
	if ( sigaction(SIGALRM,&sigact,NULL)!=0 ) {
		//warning("could not restore signal handler");
	}

	if ( sig==SIGALRM ) {
		walllimit_reached |= hard_timelimit;
		//warning("timelimit exceeded (hard wall time): aborting command");
	} else {
		//warning("received signal %d: aborting command",sig);
	}

	//write_meta("signal", "%d", sig);

	/* First try to kill graciously, then hard.
	   Don't report an already exited process as error. */
	//verbose("sending SIGTERM");
	if ( kill(-child_pid,SIGTERM)!=0 && errno!=ESRCH ) {
		//error(errno,"sending SIGTERM to command");
	}

	/* Prefer nanosleep over sleep because of higher resolution and
	   it does not interfere with signals. */
	nanosleep(&killdelay,NULL);

	//verbose("sending SIGKILL");
	if ( kill(-child_pid,SIGKILL)!=0 && errno!=ESRCH ) {
		//error(errno,"sending SIGKILL to command");
	}

	/* Wait another while to make sure the process is killed by now. */
	nanosleep(&killdelay,NULL);
}


