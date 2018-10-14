@echo off
docker build -t remingtonc/fb-photo-backup .
docker run --mount type=bind,src=%cd%\backup\,dst=/backup remingtonc/fb-photo-backup