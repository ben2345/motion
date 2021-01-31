#!/bin/bash

rclone copy /var/www/motion/files/events/ gdrive:
rclone copy /var/www/motion/files/timelapse/timelapse/ timelapse:
rclone copy /var/www/motion/files/combination/ combination:

# for entry in "rclone lsd grive:"; do
#         echo $entry
# done
