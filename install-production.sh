if ! [ -x "$(command -v docker-compose)" ]; then
  echo 'Error: docker-compose is not installed.' >&2
  exit 1
fi

domains=()
rsa_key_size=4096
data_path="./certbot"
email=""  # Adding a valid address is strongly recommended
staging=0 # Set to 1 if you're testing your setup to avoid hitting request limits

read -p "Micro Power Manager starting with production mode. Continue set up certification? (n/Y) " decision
if [ "$decision" != "Y" ] && [ "$decision" != "y" ]; then
  exit
else
  domainsDone=0
  while [ $domainsDone == 0 ]; do

    read -p "Enter domain name and press [ENTER]:" domain
    domains+=($domain)
    for key in ${!domains[*]}; do
      printf "%4d: %s\n" $key ${domains[$key]}
    done
    read -p "Add new domain name? (y/N) " newDomain
    if [ "$newDomain" != "Y" ] && [ "$newDomain" != "y" ]; then
      break
    fi
  done
  read -p "Enter a valid email address and press [ENTER]:" email
  echo "Email address :" $email

  if [ -d "$data_path" ]; then
    read -p "Existing data found for $domains. Continue and replace existing certificate? (y/N) " decision
    if [ "$decision" != "Y" ] && [ "$decision" != "y" ]; then
      exit
    fi
  fi

  if [ ! -e "$data_path/conf/options-ssl-nginx.conf" ] || [ ! -e "$data_path/conf/ssl-dhparams.pem" ]; then
    echo "### Downloading recommended TLS parameters ..."
    mkdir -p "$data_path/conf"
    curl -s https://raw.githubusercontent.com/certbot/certbot/master/certbot-nginx/certbot_nginx/options-ssl-nginx.conf >"$data_path/conf/options-ssl-nginx.conf"
    curl -s https://raw.githubusercontent.com/certbot/certbot/master/certbot/ssl-dhparams.pem >"$data_path/conf/ssl-dhparams.pem"
    echo
  fi

  for domain in "${domains[@]}"; do
    echo "### Removing old certificate for $domain ..."
    docker-compose run --rm --entrypoint "\
    rm -Rf /etc/letsencrypt/live/$domain && \
    rm -Rf /etc/letsencrypt/archive/$domain && \
    rm -Rf /etc/letsencrypt/renewal/$domain.conf" certbot
    echo
  done

  for domain in "${domains[@]}"; do
    echo "### Creating dummy certificate for $domain ..."
    path="/etc/letsencrypt/live/$domain"
    mkdir -p "$data_path/conf/live/$domain"
    docker-compose run --rm --entrypoint "\
    openssl req -x509 -nodes -newkey rsa:1024 -days 1\
      -keyout "$path/privkey.pem" \
      -out "$path/fullchain.pem" \
      -subj '/CN=localhost'" certbot
    echo
  done

  echo "### Starting nginx ..."
  docker-compose -f docker-compose-prod.yml up --force-recreate -d
  echo

  for domain in "${domains[@]}"; do
    echo "### Removing dummy certificate for $domain ..."
    docker-compose run --rm --entrypoint "\
    rm -Rf /etc/letsencrypt/live/$domain" certbot
    echo
  done

  echo "### Requesting Let's Encrypt certificates ..."

  # Select appropriate email arg
  case "$email" in
  "") email_arg="--register-unsafely-without-email" ;;
  *) email_arg="--email $email" ;;
  esac

  # Enable staging mode if needed
  if [ $staging != "0" ]; then staging_arg="--staging"; fi

  for domain in "${domains[@]}"; do
    docker-compose run --rm --entrypoint "\
    certbot certonly --webroot -w /var/www/certbot \
      $staging_arg \
      $email_arg \
      -d $domain \
      --rsa-key-size $rsa_key_size \
      --agree-tos \
      --force-renewal" certbot
    echo
  done

  echo "### Reloading nginx ..."
  docker-compose exec nginx nginx -s reload
fi
