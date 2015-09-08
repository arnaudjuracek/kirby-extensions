# IFTTT BOT plugin

A plugin for [Kirby CMS](http://getkirby.com) to automatically post from a make request. Designed for IFTTT's [Maker channel](https://ifttt.com/channels/maker/).

## Kirby installation
* Put the `iftttbot/` folder in `/site/plugins/`
* In `config.php`, create a new route (http://getkirby.com/docs/advanced/routing) :
```php
c::set('routes', array(
  array(
    'pattern' => 'iftttbot/(:any)/(:any)/(:any)',
    'action'  => function($page, $blueprint, $title){
        return create_post($page, $blueprint, $title, $_POST);
      },
    'method' => 'POST'
  ),
 ));
```

## IFTTT configuration
* Select whatever trigger you want to use.
* Select (and activate) the Action Channel *Maker*.
* Select the *Make a web request* action.
* Fill the url field with the url you routed in `config.php`. The three params are pretty self explanatory, and can be dynamic IFTTT ingredients. For example :
```
http://my-kirby-website/iftttbot/projects/image-blueprint/{{ImageTitle}}
```
* Select the `POST` method.
* Select the `application/x-www-form-urlencoded` Content Type.
* In the *Body*, write a valid [application/x-www-form-urlencoded content](http://www.w3.org/TR/html401/interact/forms.html#h-17.13.4.1). This string can embed any of your [blueprint's fields name](http://getkirby.com/docs/panel/blueprints/form-fields). If you want to skip some of them, you can : Kirby won't bother, and create empty values the next time you will save the article in the panel. In the same way, you can pass some parameters which aren't present in the blueprint : again, Kirby won't bother, and just skip them. This can be usefull to pass some additionnal options to the plugin, to make a post visible by default for example.
* Click the "Create Action" big blue button. Profit.

## Usage
Sending the IFTTT body :
```
tags=twitter,ifttt,{{UserName}}&text={{Text}}&url={{LinkToTweet}}
``` 
to the url :
```
http://my-kirby-website/iftttbot/tweets/tweet-blueprint/{{CreatedAt}}
```
will create a new article in `/content/2-tweets/august-23-2010-at-11-01pm/tweet-blueprint.txt` each time the recipe is triggered, with `tweet-blueprint.txt`containing at least :
```markdown
Title: August 23, 2010 at 11:01PM

----

Tags: twitter,ifttt,ltibbets

----

Text: 

Is eating cheerios for breakfast.

----

Url: http://twitter.com/ifttt/status/33262764734693376
```

## Names and titles
* The plugin is designed to ignore visibility status, i.e 
```
/content/1-articles/8-name-of-the-article
```
```
/content/1-articles/name-of-the-article
```
```
/content/articles/name-of-the-article
```
will all refer to the same
```
/content/articles/name-of-the-article
``` 
* If `/content/articles/name-of-the-article/` already exists, the plugin will create a new article in `/content/articles/name-of-the-article_1`, and so on. If you want to be able to override existing article, you'll have to comment the `(count($dir_matches) > 0)` condition in the `create_post()` function.
* The article's title will be passed in the `str::slug()` function, so don't be afraid to use spaces or special chars.

## Without IFTTT
Of course, you can also use the `create_post()` function to batch create post, or make some other automation without IFTTT service.



## Security
Despite having the article posted invisible by default, we personally chose to add a token in the routed url to avoid spam and various possible injections.
A simple implementation of this technic would be to get a md5 hash of the website's url or title :
```php
c::set('routes', array(
	array(
		'pattern' => md5(site()->url()) . '/(:any)/(:any)/(:any)',
		'action'  => function($page, $blueprint, $title){
			return create_post($page, $blueprint, $title, $_POST);
		},
		'method' => 'POST'
	),
));
```

## Authors
Louis Eveillard & Arnaud Juracek

## Changelog

* **1.0.0** Initial release
