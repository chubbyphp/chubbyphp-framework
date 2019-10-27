# Nginx

```nginx
server {
    server_name example.tld;

    root /var/www/example/public;
    
    access_log /var/log/nginx/example_access.log;
    error_log /var/log/nginx/example_error.log; 

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ \.php {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        fastcgi_index index.php;
        fastcgi_pass localhost:9000;
    }
}
```
