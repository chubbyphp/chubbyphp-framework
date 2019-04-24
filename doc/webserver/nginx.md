# Nginx

This documentation assumes, that the front controller is named index.php and is in the public directory.

```nginx
server {
    server_name example.tld;

    root /var/www/example/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/path/to/socket;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;

        include fastcgi_params;

        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;

        internal;
    }

    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/example_error.log;
    access_log /var/log/nginx/example_access.log;
}
```
