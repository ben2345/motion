#!/bin/bash
mkdir -p /var/www/motion/files/gif/$(date +"%Y-%m-%d")
chmod 777 /var/www/motion/files/gif/$(date +"%Y-%m-%d")
for entry in /var/www/motion/files/*.flv; do
    fileSize=$(wc -c "$entry" | awk '{print $1}')
    if [ -n "$fileSize" ]; then
    	if [ "$fileSize" -ne 0 ]; then
           fileName=${entry%.*}
           # crop=width:height:left:top
           ffmpeg -i $entry -filter:v "crop=470:920:0:170" /var/www/motion/files/gif/$(date +"%Y-%m-%d")/${fileName##*/}.gif
           chmod 777 /var/www/motion/files/gif/$(date +"%Y-%m-%d")/${fileName##*/}.gif
        fi
        rm $entry
    fi
done
