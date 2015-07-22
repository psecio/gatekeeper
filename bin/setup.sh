#!/bin/bash

echo -e "--- Executing Gatekeeper setup ----------\n"
YAML='phinx.yml'

# pre1. Check to see if the yaml file exists
if [ -r $YAML ];
then
	message="\033[31m$YAML file found, running migrations\n\033[0m"
	echo -e $message
	vendor/bin/phinx migrate
	exit
fi

# 1. ask for the database info
echo -e "> No configuration found, please enter database information:\n"
read -p "Hostname [localhost]: " hostname
if [ -z $hostname ]; then
	hostname="localhost"
fi

read -p "Database name [gatekeeper]: " dbname
if [ -z $dbname ]; then
	dbname="gatekeeper"
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


# 2. verify it can be reached
echo -e "--- Testing database connection ----------\n"

RESULT=`mysql -u $username --password=$password -e "show databases" 2>/dev/null | grep "$dbname"`

if [ "$RESULT" != "$dbname" ]; then
	echo -e "\033[31mMySQL connection failure!\n\033[0m"
	echo -e "Please verify the following:
 - The username/password you provided are correct
 - That the database has been created
 - That the user you're providing has been correctly granted permission to the database
	"
	exit;
fi

echo -e "--- Setting up configuration ----------\n"

# Our connection details are good, lets copy the file
cp ./vendor/psecio/gatekeeper/phinx.dist.yml ./phinx.yml

# And make our replacements for phinx
sed -i -e "s/%%DBNAME%%/$dbname/g" ./phinx.yml
sed -i -e "s/%%HOSTNAME%%/$hostname/g" ./phinx.yml
sed -i -e "s/%%USERNAME%%/$username/g" ./phinx.yml
sed -i -e "s/%%PASSWORD%%/$password/g" ./phinx.yml
rm ./phinx.yml-e

# Now lets move the .env file into place. If it exists, append
if [ -f ./.env ]; then
	sed -i '' -e '$a\' ./.env
	cat ./vendor/psecio/gatekeeper/.env.dist >> ./.env
else
	cp ./vendor/psecio/gatekeeper/.env.dist ./.env
fi

# And make the replacements here too
sed -i -e "s/%%DBNAME%%/$dbname/g" ./.env
sed -i -e "s/%%HOSTNAME%%/$hostname/g" ./.env
sed -i -e "s/%%USERNAME%%/$username/g" ./.env
sed -i -e "s/%%PASSWORD%%/$password/g" ./.env
rm ./.env-e

echo -e "--- Running migrations ----------\n"

# Finally we run the migrations
vendor/bin/phinx migrate

echo -e "DONE!\n\n"
