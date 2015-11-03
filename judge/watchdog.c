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

int status;
void terminate_chld(int);
char *lang;
char *prog;
pid_t pid;
struct timeval starttime, endtime;
struct tms startticks, endticks;
FILE *resfile;
FILE *errfile;
int time_limit;

int main(int argc, char * argv[]){
	
	char **run;
	errfile = fopen("submitted_codes/code.err","w");
	resfile = fopen("submitted_codes/code.res","w");
	
	if(argc<4){fprintf(errfile,"internal error\n");return -1;}
	lang = argv[1];
	time_limit = atoi(argv[3]); 

	if(!strcmp(lang,"java")){
		prog="java";
		run=(char **)malloc(sizeof(char *)*3);
		run[0] = "java";
		run[1] = argv[2];
		run[0] = NULL;
	}
	else if(!strcmp(lang,"c")){
		prog="/bin/sh";
		run=(char **)malloc(sizeof(char *)*5);
		run[0] = "sh";
		run[1] = "-c";
		run[2] = argv[2];
		run[3] = NULL;
	}
	else{
		fprintf(errfile,"internal error\n");
		return -1;
	}
	//char *run[]={"java","Main",NULL};
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
		//printf("in child\n");
		setrestrictions();
		//execvp("/bin/sh",run);
		execvp(prog,run);	
	}
	//printf("pid of child=%d,pid=%d",pid,getpid());	
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

	fflush(stdout);
		if ( ! WIFEXITED(status) ) {
			if ( WIFSIGNALED(status) ) {
	
				if ( WTERMSIG(status)==SIGXCPU ) {
					//cpulimit_reached |= hard_timelimit;
					fprintf(errfile,"signal TLE\n");
				} else {
					fprintf(errfile,"signal %d\n",WTERMSIG(status));
				}
			} else
			if ( WIFSTOPPED(status) ) {
				fprintf("signal %d\n",WSTOPSIG(status));
				//exitcode = 128+WSTOPSIG(status);
			} else {
			fprintf(errfile,"signal unknown\n");
			}
		} else {
			fprintf(errfile,"exitcode %d ",WEXITSTATUS(status));
			//exitcode = WEXITSTATUS(status);
		}
		gettimeofday(&endtime,NULL);
		times(&endticks);
		fprintf(resfile,"wall diff is %f\n",(endtime.tv_sec  - starttime.tv_sec ) +(endtime.tv_usec - starttime.tv_usec)*1E-6);
		fflush(resfile);
		fclose(resfile);
		/*usertime=(double)(endticks.tms_utime - startticks.tms_utime);
		systime=(double)(endticks.tms_stime - startticks.tms_stime);
		totaltime=usertime+systime;*/
		fflush(stdout);

}
void setrestrictions(){
		struct rlimit lim;		
		rlim_t cputime_limit = (rlim_t) time_limit;
		lim.rlim_cur = cputime_limit;
		lim.rlim_max = cputime_limit+1;
		setrlimit(RLIMIT_CPU,&lim);		
		/*lim.rlim_cur = 40000;
		lim.rlim_max= 40000;
		setrlimit(RLIMIT_AS,&lim);
		setrlimit(RLIMIT_DATA,&lim);*/
		
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

