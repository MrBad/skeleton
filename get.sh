#!/bin/bash
#
#	Modify this file to get sources from remote production server to local
#
exit
rsync -n -varlogpt --progress uk:/home/skeleton/* /home/develop/skeleton/