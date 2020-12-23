#!/bin/bash

cd ../files/events/$(date +"%Y-%m-%d")

for file in *.gif; do
   hour=`echo "$file" | cut -c1-2`
   if [ "$hour" -ge 8 ] && [ "$hour" -le 17 ]; then
      cp $file ../../combination/$file  
   fi
done

cd ../../combination/
 
nbFiles=$(ls -1 *.gif 2>/dev/null | wc -l)
nbFrames=$(($nbFiles*15))
 
# explode gifs 
for file in *.gif; do
     fullHour=`echo "$file" | cut -c1-8`
     ffmpeg -i $file -vsync 0 %03d-$fullHour.png
     rm $file
done

ffmpeg -framerate $nbFrames -pattern_type glob -i '*.png' \
  -c:v libx264 -pix_fmt yuv420p $(date +"%Y-%m-%d").mp4 -y

rm *.png

echo $nbFiles fichiers a $nbFrames images par secondes
 