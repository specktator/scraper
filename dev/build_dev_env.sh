#! /bin/bash

echo -e '\E[1;36m NodeJS INSTALLATION...\n\n'; tput sgr0

echo -e '\E[1;36m Updating apt packages ...\n\n'; tput sgr0
# sudo apt-get update

echo -e '\E[1;36m Installing dependencies ...\n\n'; tput sgr0
# sudo apt-get install git-core curl build-essential openssl libssl-dev

echo -e '\E[1;36m Setting up nodesource.com repository\n\n'; tput sgr0
# curl -sL https://deb.nodesource.com/setup | sudo bash -
# sudo apt-get install nodejs

echo -e '\E[1;36m Updating npm ...\n\n'; tput sgr0
# sudo npm -g install npm@latest

echo -e '\E[1;36m Installing bower ...\n\n'; tput sgr0
# sudo npm install -g bower

echo -e '\E[1;36m DONE!'; tput sgr0