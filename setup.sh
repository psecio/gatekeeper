#!/bin/bash

echo -e "--- Executing Gatekeeper setup ----------\n"
YAML='phinx.yml'

# pre1. Check to see if the yaml file exists
if [ -r $YAML ];
then
	message="$YAML file found, ending\n\n"
	echo -e $message
	exit
fi

# 1. ask for the database info
echo -e "> No configuration found, please enter database information:\n"
read -p "Hostname [localhost]: " hostname
if [ -z $hostname ]; then
	hostname="localhost"
fi

read -p "Username: " username
if [ -z $username ]; then
	echo -e "\033[31mUsername cannot be empty!\033[0m"
	exit;
fi

read -sp "Password: " password
if [ -z $password ]; then
	echo -e "\n\033[31mPassword cannot be empty!\033[0m"
	exit;
else
	echo -e "\n" # extra newline
fi

read -p "Database name: " dbname
if [ -z $dbname ]; then
	echo -e "\n\033[31mDatabase name cannot be empty!\033[0m"
	exit;
fi

# 2. verify it can be reached
echo -e "--- Testing database connection ----------\n"

RESULT=`mysql -u $username --password=$password -e "show databases" 2>/dev/null | grep "$dbname"`

if [ "$RESULT" != "$dbname" ]; then
	echo -e "\033[31mMySQL connection failure!\n\033[0m"
	exit;
fi


# 3. copy the yaml to the current directory
# 4. replace the placeholders with database info
# 5. run the migrations

echo -e "\n\n"
