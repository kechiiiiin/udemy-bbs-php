FROM mysql:latest

RUN set -eux \
    && ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime \
    && echo "USE mysql;" > /docker-entrypoint-initdb.d/timezones.sql \
    && mysql_tzinfo_to_sql /usr/share/zoneinfo >> /docker-entrypoint-initdb.d/timezones.sql \
    && mkdir /var/log/mysql/ \
    && chown -R mysql. /var/log/mysql/

RUN mkdir -p /var/lib/mysql-files

COPY conf/my.cnf /etc/mysql/my.cnf