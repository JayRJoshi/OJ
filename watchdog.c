#include<unistd.h>
#include<stdio.h>
#include<stdlib.h>
#include<signal.h>
#include<sys/resource.h>
#include<sys/time.h>
#include<sys/times.h>
#include<errno.h>
#include<stdarg.h>

extern int errno;
void sig_chld(int);
void sig_fpa(int);
void setrestrictions();

void report(const char *, ...) __attribute__((format (printf, 1, 2)));
void error(int, const char *, ...) __attribute__((format (printf, 2, 3)));



int status;
void terminate_chld(int);
pid_t pid;
struct timeval starttime, endtime;
struct tms startticks, endticks;

/*********** Log functions start***********/
void report(const char *format, ...)
{
	va_list ap;
	va_start(ap,format);
	//fprintf(stderr,"%s: log: ",progname);
	vfprintf(stderr,format,ap);
	fprintf(stderr,"\n");

	va_end(ap);
}

void error(int errnum, const char *format, ...)
{
	va_list ap;
	va_start(ap,format);

	//fprintf(stderr,"%s",progname);

	if ( format!=NULL ) {
		fprintf(stderr,": ");
		vfprintf(stderr,format,ap);
	}
	if ( errnum!=0 ) {
		fprintf(stderr,": %s",strerror(errnum));
	}
	if ( format==NULL && errnum==0 ) {
		fprintf(stderr,": unknown error");
	}

	//fprintf(stderr,"\nTry `%s --help' for more information.\n",progname);
	va_end(ap);

//	write_meta("internal-error","%s","runguard error");

	//exit(exit_failure);
}


/*********** Log functions End *************/
int main(){
	
	char *run[]={"sh","-c","./fire.o",NULL};
	if(signal(SIGCHLD,sig_chld)==SIG_ERR){
		perror("signal error");
	}
	signal(SIGALRM,terminate_chld);
	alarm(5);
	//signal(SIGALRM,chil
	gettimeofday(&starttime,NULL);	
	times(&startticks);
	if((pid=fork())<0){
		perror("signal error");	
	}
	else if(pid==0){
		setpgid(0,0);
		printf("in child\n");
		setrestrictions();
		execvp("/bin/sh",run);	
	}
	printf("pid of child=%d,pid=%d",pid,getpid());	
	fflush(stdout);	
	//sleep(3);
	//kill(-pid,SIGKILL);
	//while(1){printf("A");}
	pid=wait(&status);
	//nanosleep(1,NULL);
	fflush(stdout);
	//sleep(100000);
	exit(0);
}
void terminate_chld(int signo){
	kill(-pid,SIGKILL);
}
void sig_chld(int signo){
	long ticks_per_second = sysconf(_SC_CLK_TCK);
	double usertime,systime,totaltime;
	pid_t pid;
	//int status;
	report("SIGCHLD received\n");
	fflush(stdout);
		if ( ! WIFEXITED(status) ) {
			if ( WIFSIGNALED(status) ) {
				printf("signaled\n");
				if ( WTERMSIG(status)==SIGXCPU ) {
					//cpulimit_reached |= hard_timelimit;
					printf("sigxcpu\n");
				} else {
					error(errno,"command terminated with signal %d",WTERMSIG(status));
				}
				
			} else
			if ( WIFSTOPPED(status) ) {
				printf("command stopped with signal %d",WSTOPSIG(status));
				//exitcode = 128+WSTOPSIG(status);
			} else {
			error(errno,"unknown\n");
			}
		} else {
			error(errno,"exitcode %d ",WEXITSTATUS(status));
			//exitcode = WEXITSTATUS(status);
		}
		gettimeofday(&endtime,NULL);
		times(&endticks);
		printf("wall diff is %f\n",(endtime.tv_sec  - starttime.tv_sec ) +(endtime.tv_usec - starttime.tv_usec)*1E-6);
		usertime=(double)(endticks.tms_utime - startticks.tms_utime);
		systime=(double)(endticks.tms_stime - startticks.tms_stime);
		totaltime=usertime+systime;
		printf("usertime %lf,systime %lf, totaltime %lf\n",usertime,systime,totaltime);
		
		fflush(stdout);

}
void setrestrictions(){
		struct rlimit lim;		
		rlim_t cputime_limit = (rlim_t) 2;
		lim.rlim_cur = cputime_limit;
		lim.rlim_max = cputime_limit+1;
		setrlimit(RLIMIT_CPU,&lim);		
		//lim.rlim_cur = 40;
		//lim.rlim_max= 400000;
		//setrlimit(RLIMIT_AS,&lim);
		//setrlimit(RLIMIT_DATA);
		
		/*************testing starts***************
		getrlimit(RLIMIT_NPROC,&lim);
		lim.rlim_cur = 0;
		lim.rlim_max=0;
		if(setrlimit(RLIMIT_NPROC,&lim)!=0){
			printf("***********  error *************\n");
}
*/
	fflush(stdout);
}

