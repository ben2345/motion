#!/bin/bash

timelapseTempDayly=all_time/timelapse-temp-dayly.flv

timelapseNew=all_time/timelapse-new.flv

timelapseFinal=all_time/timelapse.flv

# copy & convert files
cd /var/www/motion/files/
for file in *.mpg; do
   hour=`echo "$file" | cut -c1-2`
   if [ "$hour" -ge 7 ] && [ "$hour" -le 17 ]; then
      ffmpeg -i $file -filter:v "crop=470:920:0:170" /var/www/motion/files/timelapse/$hour.flv -y
   else
	 rm $file     
   fi
done

cd timelapse/

# merge files to dayly Temp timelapse
flvFiles=*.flv
for file in $flvFiles; do
    echo "file '$file'" >> timelapses.txt;
done
# timelapse du jour
ffmpeg -f concat -safe 0 -i timelapses.txt -c copy $timelapseTempDayly

# timelapse du jour + ancien timeLapse => timelapseNew
ffmpeg -i "concat:$timelapseFinal|$timelapseTempDayly" -c copy $timelapseNew -y

# copie de timelapseNew a la place de timeLapseFinal
rm $timelapseFinal
cp $timelapseNew $timelapseFinal

# clean
rm timelapses.txt $timelapseNew $timelapseTempDayly *.flv
 