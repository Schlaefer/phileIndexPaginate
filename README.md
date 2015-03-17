# Paginate Folder Plugin for PhileCMS #

Output all files in a folder on multiple pages with navigation between pages.

[Project Home](https://github.com/Schlaefer/phileIndexPaginate)

### 1.1 Installation (composer) ###

```shell
php composer.phar require siezi/phile-index-paginate:*
```

### 1.2 Installation (Download)

* Install [Phile](https://github.com/PhileCMS/Phile)
* Put this plugin into `plugins/siezi/phileIndexPaginate`

### 2. Activation

After you have installed the plugin. You need to add the following line to your `config.php` file:

```php
$config['plugins']['siezi\\phileIndexPaginate'] = ['active' => true];
```

### 3. Start: Simple Blog Example ###

- Create a folder e.g. `content/blog` 
- Put an *empty* `index.md` into it (the empty index file tells the plugin to paginate the folder content)
- Put all your postings into it and blog along

### 4. Config ###

See `config.php`.