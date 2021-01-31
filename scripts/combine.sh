#!/bin/bash

eventPath=/var/www/motion/files/events/$(date +"%Y-%m-%d")
combinationPath=/var/www/motion/files/combination
cd $eventPath

# for file in $eventPath/*.gif; do
#     fileName="$(basename $file)"
#     hour=`echo "$fileName" | cut -c1-2`
#     if [ "$hour" -ge 9 ] && [ "$hour" -le 16 ]; then
#         cp $file $combinationPath/$fileName 
#     fi
# done

cd $combinationPath

# currentFileNumber=0
# for file in *.gif; do
#      fullHour=`echo "$file" | cut -c1-8`
#      currentFileNumber=$((currentFileNumber+1))
#      ffmpeg -i $file -vsync 0 $currentFileNumber-%03d.png
# done


nbFiles=$(ls -1 *.gif 2>/dev/null | wc -l)

  convert    12-10-43.gif   -coalesce  -set delay 0 \
          -bordercolor red -border 0 +matte    null: \
          -duplicate 1,1--2 -compose difference -layers composite \
          +delete -compose plus -background black -flatten \
          -separate -flatten -threshold 0 areas.gif




# convert '(' 1-001.png -flatten -grayscale Rec709Luminance ')' \
#         '(' 1-002.png -flatten -grayscale Rec709Luminance ')' \
#         '(' -clone 0-1 -compose darken -composite ')' \
#         -channel RGB -combine diff.png





 # compare -compose src 1-001.png 1-002.png difference.png

#   composite 1-001.png 1-002.png -compose difference difference_frames.png

#   convert difference_frames.png  -fuzz 10% -transparent black result.png  


#  compare -metric PSNR 1-001.png 1-002.png difference1.png

#  compare -compose src 1-001.png 1-002.png difference2.png
# # magick difference.png -transparent "#CCCCCC" alpha.png


#  compare -metric RMSE -subimage-search 1-001.png 1-002.png similarity.gif
 
# for i in `seq 1 $nbFiles`
# do

#     echo "$i"

# done








# ffmpeg -framerate $nbFrames -pattern_type glob -i '*.png' \
#   -c:v libx264 -pix_fmt yuv420p $(date +"%Y-%m-%d").mp4 -y

# rm *.png *.gif

# totalImages=$(($nbFrames/$nbFiles))
# finalSeconds=$(($totalImages/60))

#echo $totalImages iameges au total donc  $finalSeconds secondes

# echo $nbFiles fichiers a $nbFrames images par secondes
 