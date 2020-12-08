#!/bin/bash
#cd /var/www/motion/files/gif/
rclone copy /var/www/motion/files/gif/ gdrive:



for entry in "rclone lsd grive:"; do
        echo $entry
done




