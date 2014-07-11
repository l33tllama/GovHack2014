#!/bin/bash
git add *
if [-z $1] 
	then
	git commit -m "I forgot to add a message."
else
	git commit -m "$1"
fi
git push origin master