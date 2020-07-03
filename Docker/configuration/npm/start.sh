#!/bin/sh
echo "copy start.sh to target directory"

echo "write into file"
echo "npm install" >> /home/node/app/start.sh
echo "npm run production" >> /home/node/app/start.sh

chmod +x /home/node/app/start.sh
echo "start installing and building"
/bin/bash /home/node/app/start.sh
cat /home/node/app.start.sh
echo "delete copy"
rm /home/node/app/start.sh
echo "NPM INSTALLED and BUILD SUCCESSFULLY"