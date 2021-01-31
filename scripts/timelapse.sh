#!/bin/bash

timelapseDayly=/var/www/motion/files/timelapse/timelapse/timelapse-dayly.flv
timelapseNew=/var/www/motion/files/timelapse/timelapse/timelapse-new.flv
timelapseFinal=/var/www/motion/files/timelapse/timelapse/timelapse.flv
timelapseFinalMp4=/var/www/motion/files/timelapse/timelapse/timelapse.mp4
hourFile=/var/www/motion/files/timelapse/hours.txt
dayFile=/var/www/motion/files/timelapse/days.txt

# copie & crop+convertion
cd /var/www/motion/files/
for file in *.mpg; do
   hour=`echo "$file" | cut -c1-2`
   if [ "$hour" -ge 8 ] && [ "$hour" -le 17 ]; then
      ffmpeg -i $file /var/www/motion/files/timelapse/$hour.flv   
   fi
   rm $file
done

cd /var/www/motion/files/timelapse/

# timelapse du jour
flvFiles=*.flv
for file in $flvFiles; do
    echo "file '$file'" >> $hourFile;
done
ffmpeg -f concat -safe 0 -i $hourFile -c copy $timelapseDayly

# timelapse du jour + timeLapse final => timelapseNew
echo "file '$timelapseFinal'" >> $dayFile;
echo "file '$timelapseDayly'" >> $dayFile;
ffmpeg -f concat -safe 0 -i $dayFile -c copy $timelapseNew
 
# copie de timelapseNew a la place du timeLapse final
rm $timelapseFinal
cp $timelapseNew $timelapseFinal

# clean
rm /var/www/motion/files/timelapse/*.flv $hourFile $dayFile $timelapseDayly $timelapseNew 

# convertion  mp4
ffmpeg -i $timelapseFinal -c:v libx264 -crf 19 -strict experimental $timelapseFinalMp4 -y