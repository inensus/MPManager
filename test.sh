
 echo " Starting web services please wait."
 echo $( docker-compose -f docker-compose-prod-non-domain.yml up --build --force-recreate -d)
 echo "Web services started "