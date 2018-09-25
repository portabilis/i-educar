# este arquivo deve ser usado para instalar as dependencias do i-educar,
#
# para instalar o pear no ubuntu: sudo apt-get install php-pear
# ou para atualizar uma instalação já existente: sudo pear upgrade pear

echo -e "\n\n** Instalando dependencias i-Educar"

sudo pear install XML_RPC2

# mailer
sudo pear install Mail
sudo pear install Net_SMTP

# recaptcha
sudo pear install Services_ReCaptcha

sudo service apache2 restart