# Lissi on InfinityFree

This folder is the uploadable PHP/MySQL version of Lissi. Upload everything inside this folder to your InfinityFree `htdocs` directory.

## Install

1. Create an InfinityFree account and a free hosting subdomain.
2. In the Control Panel, create a MySQL database and user. Copy the exact database host, name, username, and password.
3. Open `schema.sql` in phpMyAdmin and run it once.
4. Copy `config.php` to `config.local.php` and fill in the four MySQL values. Change `admin_password` to a strong password.
5. Upload the contents of this folder to `htdocs` using File Manager or FTP.
6. Visit `https://YOUR-SUBDOMAIN.epizy.com/`.
7. Admin is at `https://YOUR-SUBDOMAIN.epizy.com/admin/`.

## Important

- Do not upload `config.local.php` to GitHub. It is already ignored by the root `.gitignore` pattern for local config files.
- InfinityFree free hosting does not provide reliable outbound API access for every service. Anderson delivery integration should be added after confirming their API allows requests from the host. Until then, edit Bureau and home rates in `checkout.php`.
- This uses Cash on Delivery only. Orders are stored in MySQL and stock is reduced when an order is placed.
- Enable HTTPS in InfinityFree if available for your domain. Never store payment card data.
