FROM docker/whalesay:latest
LABEL Name=garageparrot Version=0.0.1
RUN apt-get -y update && apt-get install -y fortunes
CMD ["sh", "-c", "/usr/games/fortune -a | cowsay"]
# Utiliser une image PHP avec Apache
FROM php:8.0-apache

# Installation des extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Copier les fichiers de l'application dans le conteneur
COPY . /var/www/html

# Changer le propriétaire des fichiers pour Apache
RUN chown -R www-data:www-data /var/www/html

# Activer le module rewrite d'Apache
RUN a2enmod rewrite

# Exposer le port 80
EXPOSE 80

# Commande par défaut pour démarrer Apache
CMD ["apache2-foreground"]
