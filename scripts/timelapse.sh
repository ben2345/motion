
#!/bin/bash

newFilePath=/var/www/motion/files/`TZ=GMT+24 date +%Y-%m-%d`-timelapse.mpg;
timelapseFilePath=/var/www/motion/files/timelapse/timelapse.flv
timelapseFilePathTemp=/var/www/motion/files/timelapse/timelapse-temp.flv
timelapseFilePathNew=/var/www/motion/files/timelapse/timelapse-new.flv

if test -f "$timelapseFilePath"; then
    if test -f "$newFilePath"; then
        ffmpeg -i $newFilePath -filter:v "crop=470:920:0:170" $timelapseFilePathNew -y
    fi
    cp -v $timelapseFilePath $timelapseFilePathTemp
    if test -f "$timelapseFilePath"; then
        ffmpeg -i "concat:$timelapseFilePathTemp|$timelapseFilePathNew" -c copy $timelapseFilePath -y
    fi
fi

rm $timelapseFilePathTemp $timelapseFilePathNew