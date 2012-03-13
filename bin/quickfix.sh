#!/bin/bash
BASE="$HOME/projects/ocstoreru.git/trunk"
mkdir -p ${BASE}/image/cache/data
touch ${BASE}/{,admin/}config.php
chmod 644 ${BASE}/{,admin/}config.php

chmod 777 ${BASE}/image
chmod 777 ${BASE}/download
chmod 777 ${BASE}/image/cache
chmod 777 ${BASE}/image/cache/data
chmod 777 ${BASE}/image/data
chmod 777 ${BASE}/system/cache
chmod 777 ${BASE}/system/logs
