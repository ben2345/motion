#!/bin/bash

# copie & crop+convertion
cd /var/www/motion/files/
for file in *.mpg; do
   hour=`echo "$file" | cut -c1-2`
   if [ "$hour" -ge 8 ] && [ "$hour" -le 17 ]; then
      ffmpeg -i $file -filter:v "crop=470:920:0:170" /var/www/motion/files/timelapse/$hour.flv   
   fi
   rm $file
done

cd timelapse/

# todo rename folder -> timelapse
timelapseDayly=timelapse/timelapse-dayly.flv
timelapseNew=timelapse/timelapse-new.flv
timelapseFinal=timelapse/timelapse.flv
$timelapseFinalMp4==timelapse/timelapse.mp4

# timelapse du jour
flvFiles=*.flv
for file in $flvFiles; do
    echo "file '$file'" >> hours.txt;
done
ffmpeg -f concat -safe 0 -i hours.txt -c copy $timelapseDayly

# timelapse du jour + timeLapse final => timelapseNew
echo "file '$timelapseFinal'" >> days.txt;
echo "file '$timelapseDayly'" >> days.txt;
ffmpeg -f concat -safe 0 -i days.txt -c copy $timelapseNew

# copie de timelapseNew a la place du timeLapse final
rm $timelapseFinal
cp $timelapseNew $timelapseFinal

# clean
rm *.flv hours.txt days.txt $timelapseDayly $timelapseNew 

# convertion  mp4
ffmpeg -i $timelapseFinal -c:v libx264 -crf 19 -strict experimental $timelapseFinalMp4 -y