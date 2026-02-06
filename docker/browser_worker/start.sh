#!/bin/bash
# Start Xvfb and run worker
export DISPLAY=:99
Xvfb :99 -screen 0 1920x1080x24 &
sleep 2
python -u worker.py
