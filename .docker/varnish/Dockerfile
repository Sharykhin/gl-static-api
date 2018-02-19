FROM ubuntu:14.04.2

# Update the package repository and install applications
RUN apt-get update -qq && \
  apt-get upgrade -yqq && \
  apt-get -yqq install varnish && \
  apt-get -yqq install bash && \
  apt-get -yqq clean

# Make our custom VCLs available on the container
ADD default.vcl /etc/varnish/default.vcl

ENV VARNISH_PORT 80
ENV VARNISH_MEMORY 100M

# Expose port 80
EXPOSE 80

ADD start.sh /start.sh
# Expose volumes to be able to use data containers
# VOLUMES ["/var/lib/varnish", "/etc/varnish"]

#CMD tail -f /dev/null

CMD ["/start.sh"]