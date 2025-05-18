FROM mysql:8.0

# Sao chép file cấu hình MySQL
COPY my.cnf /etc/mysql/conf.d/

# Sao chép file SQL khởi tạo
COPY db_product.sql /docker-entrypoint-initdb.d/
COPY db_uses.sql /docker-entrypoint-initdb.d/

# Thiết lập quyền thực thi cho script
RUN chmod 0444 /docker-entrypoint-initdb.d/db_product.sql
RUN chmod 0444 /docker-entrypoint-initdb.d/db_uses.sql

# Expose port 3306
EXPOSE 3306

# Thiết lập thư mục làm việc
WORKDIR /var/lib/mysql

# Khởi động MySQL với cấu hình UTF-8
CMD ["mysqld", "--character-set-server=utf8mb4", "--collation-server=utf8mb4_unicode_ci"]