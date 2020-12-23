#!/bin/bash

rclone copy /var/www/motion/files/events/ gdrive:
rclone copy /var/www/motion/files/timelapse/timelapse/ timelapse:

# for entry in "rclone lsd grive:"; do
#         echo $entry
# done