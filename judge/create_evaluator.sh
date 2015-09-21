#!/bin/bash
#Run this script as su
#Script which sets up user which creates user which compiles and executes user  programs in restrictive environment.
#Make sure first environment variables are setup for online judge
GROUP_NAME=restricted_env
USER_NAME=code_evaluator

id -g $GROUP_NAME > /dev/null 2>&1 && \
echo "Group $GROUP_NAME already exists" && exit 1

groupadd $GROUP_NAME
if [ $? -eq 0 ]; then
	echo "Validating group name..."
	grep $GROUP_NAME /etc/group
	id -u $USER_NAME
	if [ $? -ne 0 ]; then
		useradd -g $GROUP_NAME $USER_NAME && iptables -A OUTPUT -m owner --gid-owner $GROUP_NAME -j DROP && echo "Successfully created user"
	else
		echo "Problem while adding user..."
		exit 1
		
	fi
else
	echo "Problem while adding group.."
	exit 1
fi

HOMEDIR=/home/$USER_NAME
mkdir $HOMEDIR
cd $HOMEDIR
mkdir compile
mkdir logs
echo "successfully addes dirs"
chown $USER_NAME:$GROUP_NAME -R $HOMEDIR
