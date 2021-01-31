#!/bin/bash

MACadd="A4:C1:38:98:FB:81"
  
hciconfig hci0 down
hciconfig hci0 up
 
values=$(timeout 15 gatttool -b $MACadd --char-write-req --handle=0x0038 --value=0100 --listen | grep --max-count=1 "Notification handle")
# Notification handle = 0x0036 value: 7c 08 2a 97 0c

first=`echo "$values" | cut -c37-38`
second=`echo "$values" | cut -c40-41`

temperature="${second}${first}"
temperature=$((0x$temperature))

    if [ "$temperature" -gt "10000" ];
    then
        temperature=$((-65536 + $temperature))
    fi 
temperature=$(echo "scale=2;$temperature/100" | bc)

humidity=`echo "$values" | cut -c43-44`
humidity=$((0x$humidity))
echo "${temperature}|${humidity}"
 