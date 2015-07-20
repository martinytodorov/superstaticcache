# Super Static Cache

This is a Yii Component that allows you to cache the php output in an html file. This way you can have a static homepage for example that refreshes every 5 minutes. This should be used only with high traffic websites. COutputCache is very good for normal usage, but in my case I needed to serve an enormous amount of queries, so this component kept the server cool.

## Installation

Put the php file in *protected/components*

## Usage

Load it in *config/main.php* in the *components* array with the following:

```
// Super Static Cache definition
'superStaticCache' => array(
	'class' => 'SuperStaticCache',
	'enable' => true,
	'duration' => 300, //equals 5 minutes
	'cacheHomepage' => true,
	'controllers' => array(
		'article'
	),
),
```

## Contributing

1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -m 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request :D

## Credits

Martin Todorov

## License

GPL Public License