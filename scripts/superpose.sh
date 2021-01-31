#!/bin/bash


rm *.png *.gif

eventPath=/var/www/motion/files/events/$(date +"%Y-%m-%d")
superposePath=/var/www/motion/files/superpose
cd $eventPath

for file in $eventPath/*.gif; do
    fileName="$(basename $file)"
    hour=`echo "$fileName" | cut -c1-2`
    if [ "$hour" -ge 9 ] && [ "$hour" -le 16 ]; then
        cp $file $superposePath/$fileName 
    fi
done

cd $superposePath


 
nbFiles=$(ls -1 *.gif 2>/dev/null | wc -l)
nbFrames=$(($nbFiles*15))

# explode gifs 
for file in *.gif; do
     fullHour=`echo "$file" | cut -c1-8`
     ffmpeg -i $file -vsync 0 %03d-$fullHour.png
done

convert *.png -background none -compose dst_over -flatten output.png

# ffmpeg -framerate $nbFrames -pattern_type glob -i '*.png' \
#   -c:v libx264 -pix_fmt yuv420p $(date +"%Y-%m-%d").mp4 -y



# totalImages=$(($nbFrames/$nbFiles))
# finalSeconds=$(($totalImages/60))

#echo $totalImages iameges au total donc  $finalSeconds secondes

# echo $nbFiles fichiers a $nbFrames images par secondes
 