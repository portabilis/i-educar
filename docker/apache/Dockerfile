FROM httpd:2.4-alpine

# Add our docker.apache.conf file to bottom of the main apache configuration
# Copy backup of original to tmp, append ours and replace .conf file.

RUN touch /tmp/new_apache.conf; \
    cat /usr/local/apache2/conf/original/httpd.conf > /tmp/new_apache.conf; \
    echo "Include /usr/local/apache2/conf.d/docker.apache.conf" >> /tmp/new_apache.conf; \
    mv /tmp/new_apache.conf /usr/local/apache2/conf/httpd.conf;
