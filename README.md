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

After you have installed the plugin. You need to add the following line before other plugins to your `config.php` file:

```php
$config['plugins']['siezi\\phileIndexPaginate'] = ['active' => true];
```

### 3. Start ###

Put `(folder-index: current)` on a page (e.g. `index.md`). In that place the paginated folder will be displayed.

Use `(folder-index: recursive)` to include all subfolder and pages.

### 4. Config ###

See `config.php`.