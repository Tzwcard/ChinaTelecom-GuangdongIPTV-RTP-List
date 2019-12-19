@echo off

FOR /L %%i IN (0,1,1) DO (
  FOR /L %%j IN (1,1,255) DO (
    echo 239.77.%%i.%%j.
    ffmpeg -i "rtp://239.77.%%i.%%j:5146" -vframes 1 %%i_%%j.jpeg >NUL 2>NUL
rem or
rem ffmpeg -i "http://udpxy:port/rtp/239.77.%%i.%%j:5146" -vframes 1 %%i_%%j.jpeg >NUL 2>NUL
  )
)
