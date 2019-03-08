/*
test how the clocks behave on suspend.
REALTIME and BOOTTIME both show a jump in time, whereas MONOTONIC doesn't

CLOCK_MONOTONIC diff 2.00000
CLOCK_BOOTTIME diff 2.00000
CLOCK_REALTIME diff 11.00000
CLOCK_MONOTONIC diff 2.00000
CLOCK_BOOTTIME diff 11.00000
CLOCK_REALTIME diff 2.00000
CLOCK_MONOTONIC diff 2.00000
CLOCK_BOOTTIME diff 2.00000
CLOCK_REALTIME diff 2.00000
CLOCK_MONOTONIC diff 2.00000
CLOCK_BOOTTIME diff 2.00000

*/
#include <time.h>
#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <stdint.h>
#include <sys/select.h>
#include <sys/time.h>
#include <sys/types.h>
// FIXME autoconf: check for sys/timerfd.h and for TFD_TIMER_CANCEL_ON_SET
#include <sys/timerfd.h>
#ifndef TFD_TIMER_CANCEL_ON_SET
#  define TFD_TIMER_CANCEL_ON_SET (1 << 1)
#endif
#define TIME_T_MAX (time_t)((1UL << ((sizeof(time_t) << 3) - 1)) - 1)

double timespec_diff(struct timespec *t1, struct timespec *t2) {
	return ((t2->tv_sec-t1->tv_sec)*1000000000.0 + (t2->tv_nsec-t1->tv_nsec))/1000000000.0;
}

void ini_time_ref(struct timespec *timeref_monotonic, struct timespec *timeref_boottime) {
       clock_gettime(CLOCK_MONOTONIC, timeref_monotonic);
       clock_gettime(CLOCK_BOOTTIME, timeref_boottime);
}

double suspend_time(struct timespec *timeref_monotonic, struct timespec *timeref_boottime) {
	struct timespec tpm, tpb;
	double tpm_diff, tpb_diff;
       clock_gettime(CLOCK_MONOTONIC, &tpm);
       clock_gettime(CLOCK_BOOTTIME, &tpb);

	tpm_diff = timespec_diff(timeref_monotonic, &tpm);	
	tpb_diff = timespec_diff(timeref_boottime, &tpb);	

	ini_time_ref(timeref_monotonic, timeref_boottime);

	return (tpb_diff - tpm_diff);
}

void add_to_select_set(int fd, fd_set * set, int *max_fd)
    /* add fd to set, and update max_fd if necessary (for select()) */
{
    FD_SET(fd, set);
    if (fd > *max_fd)
        *max_fd = fd;
}



#define handle_error(msg) \
	{ perror(msg); exit(1); }

int main(int argc, char *argv[]) {

	struct timespec res, tpr, tpr_prev, tpm, tpm_prev, tpb, tpb_prev;

	struct itimerspec new_value = {
		.it_value.tv_sec = TIME_T_MAX,
	};
	int timer_cancel_on_set_fd;
	uint64_t exp;
	
	fd_set read_set, master_set;
	int set_max_fd = 0;
	FD_ZERO(&read_set);
	FD_ZERO(&master_set);
	struct timeval timeout;
	int retcode;

	struct timespec timeref_monotonic, timeref_boottime;

	timer_cancel_on_set_fd = timerfd_create(CLOCK_REALTIME, 0);
	if (timer_cancel_on_set_fd == -1) {
		handle_error("timerfd_create");
	}

	if (timerfd_settime(timer_cancel_on_set_fd, TFD_TIMER_ABSTIME|TFD_TIMER_CANCEL_ON_SET, &new_value, NULL) == -1) {
		handle_error("timerfd_settime");
	}

	add_to_select_set(timer_cancel_on_set_fd, &master_set, &set_max_fd);

	printf("TIME_T_MAX=%ld\n", TIME_T_MAX);

       clock_getres(CLOCK_REALTIME, &res);
	printf("RESOLUTION CLOCK_REALTIME %ld.%09ld\n", res.tv_sec, res.tv_nsec);

       clock_getres(CLOCK_MONOTONIC, &res);
	printf("RESOLUTION CLOCK_MONOTONIC %ld.%09ld\n", res.tv_sec, res.tv_nsec);

       clock_getres(CLOCK_BOOTTIME, &res);
	printf("RESOLUTION CLOCK_BOOTTIME %ld.%09ld\n", res.tv_sec, res.tv_nsec);

	
	ini_time_ref(&timeref_monotonic, &timeref_boottime);

       clock_gettime(CLOCK_REALTIME, &tpr_prev);
       clock_gettime(CLOCK_MONOTONIC, &tpm_prev);
       clock_gettime(CLOCK_BOOTTIME, &tpb_prev);

	while(1) {
		read_set = master_set;
		timeout.tv_sec = 10;
		timeout.tv_usec = 0;
		retcode = select(set_max_fd+1, &read_set, NULL, NULL, &timeout);

	printf("\n");

		if (retcode > 0 && FD_ISSET(timer_cancel_on_set_fd, &read_set)) {
			double st;
			st = suspend_time(&timeref_monotonic, &timeref_boottime);

			printf("detected time change (%.09f): just out of suspend?\n", st);
			/* we don't need the data from that fd, but we must read it 
			* or select() would return immediately again */
			read(timer_cancel_on_set_fd, &exp, sizeof(uint64_t));
		}
			
       clock_gettime(CLOCK_REALTIME, &tpr);
       clock_gettime(CLOCK_MONOTONIC, &tpm);
       clock_gettime(CLOCK_BOOTTIME, &tpb);

	printf("CLOCK_REALTIME diff %.09f timespec_diff\n", timespec_diff(&tpr_prev, &tpr));
	printf("CLOCK_MONOTONIC diff %.09f timespec_diff\n", timespec_diff(&tpm_prev, &tpm));
	printf("CLOCK_BOOTTIME diff %.09f timespec_diff\n", timespec_diff(&tpb_prev, &tpb));

	tpr_prev = tpr;	
	tpm_prev = tpm;	
	tpb_prev = tpb;	
	}

}
