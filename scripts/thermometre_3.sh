#!/bin/bash
bt="58:2D:34:32:DC:6B"
hciconfig hci0 down; hciconfig hci0 up

data=$(/usr/bin/timeout 20 /usr/bin/gatttool -b $bt --char-write-req --handle=0x10 -n 0100 --listen | grep "Notification handle" -m 2)

temp=$(echo $data | tail -1 | cut -c 42-54 | xxd -r -p)
humid=$(echo $data | tail -1 | cut -c 64-74 | xxd -r -p)
 
if [ -n "$temp" ]; then
    echo "${temp}|${humid}"
fi
 
hciconfig hci0 down; hciconfig hci0 up
