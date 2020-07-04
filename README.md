##This is the codebase for the Washtenaw County's Drupal 8 website

This project involved creating a virtual machine on my Apple Macbook Pro,
creating a Drupal 8 website on that virtual machine, and then migrating the
the exisiting Washtenaw County Drupal 7 website to that "virtual" Drupal 8
machine.  I used Jeff [geerlingguy] Geerling's excellent youtube "how to"
series go guide me through this process.

The code on my Macbook Pro "host" machine is contained in the wash-gop-com
directory.  Jeff created a Docker container for this project. I decided to
use his vagrant virtual technology with which I am already familiar. Inside
the wash-gop-com directory is a directory called Vagrantcode; this directory
houses the code necessary to create the virtual machine that I used.

As did Jeff, I have automated the process of "installing" drupal.  In doing
so, I've learned a bit about what is actually happening when you do an
installation: specifically, that this is the point in the process that
your drupal database is set up and initialized.  Here is the code
within my config.yml file that does the drupal installation:

```
# Run specified scripts before or after VM is provisioned. 
# Use {{ playbook_dir }} to reference the provisioning/ folder in Drupal VM
# or {{ config_dir }} to reference the directory where your `config.yml` is.
pre_provision_scripts: []
post_provision_scripts:
     - "{{ playbook_dir }}/../wcgopsite/scripts/drushsiteinstall.sh"
``` 

Here is the shell script drushsiteinstall.sh that does the drupal installation.
Note that we also re-set the admin password so we do not have to remember a
new password with each drupal install.

```
cd /var/www/drupal8vm/web
drush site:install minimal
  --db-url="mysql://drupal:drupal@localhost:3306/drupal" --site-name="WCGOP"  -y 
drush upwd "admin" "wd17hh21"
```

After downloading and installing composer in the wash-gop-com directory
[instructions for doing this can be found at http://getcomposer.org/download],
we create the initial codebase for the website via the following command:

```
composer -n --prefer-dist create-project drupal/recommended-project:^8
```

We also add a .gitignore file to the wash-gop-com directory that minimizes the
code that actually gets uploaded to the repository.


We also added the block_content module [ this was done from the Extend menu]
and also added three additional modules needed for migration as follows:

```
	COMPOSER_MEMORY_LIMIT=-1 composer require drupal/migrate_upgrade:^3.2
	    drupal/migrate_plus:^4.2 drupal/migrate_tools:^4.5
```

At this point we were prepared to begin doing the migration work, and this is
where we made our first commit to the wash-gop-com repository.