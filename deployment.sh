server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name _;
    root /home/easy_univ/code/public;

    index index.html index.htm index.php index.nginx-debian.html;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}






[program:easy_univ_horizon] 
process_name=%(program_name)s
command=php /home/easy_univ/code/artisan horizon
autostart=true
autorestart=true
user=easy_univ
redirect_stderr=true
stdout_logfile=/home/easy_univ/code/storage/logs/horizon.log
stopwaitsecs=3600