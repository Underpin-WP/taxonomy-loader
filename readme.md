# Underpin Taxonomy Loader

Loader That assists with registering taxonomy taxonomies to a WordPress website.

## Installation

### Using Composer

`composer require underpin/taxonomy-loader`

### Manually

This plugin uses a built-in autoloader, so as long as it is required _before_
Underpin, it should work as-expected.

`require_once(__DIR__ . '/underpin-taxonomies/taxonomies.php');`

## Setup

1. Install Underpin. See [Underpin Docs](https://www.github.com/underpin-wp/underpin)
1. Register new taxonomies menus as-needed.

## Example

A very basic example could look something like this.

```php
// Register taxonomy
underpin()->taxonomies()->add( 'taxonomy', [
	'post_type'   => 'post',                        // Defaults to post.
	'id'          => 'ingredients',                 // Required. See register_post_type
	'description' => 'Ingredients for this recipe', // Human-readable description.
	'name'        => 'Ingredients',                 // Human-readable name. Usually plural. Will set "label" argument if name is unset in args.
	'args'        => [                              // Default atts. See register_post_type
		'public' => true,
	],
] );
```

Alternatively, you can extend `Taxonomy` and reference the extended class directly, like so:

```php
underpin()->taxonomies()->add('taxonomy-key','Namespace\To\Class');
```

## Querying

A Taxonomy instance includes a method, called `query`, which serves as a wrapper for `new WP_Term_Query`.

This encapsulates queries for this taxonomy in a method, and gives you a place to override exactly _how_ this taxonomy
is queried, should you decide to extend the class.

```php
underpin()->taxonomies()->get( 'taxonomy' )->query();
```

## Editing Terms

Like querying, Taxonomy instances includes a method called `save` which serves as a wrapper for `wp_insert_term`
and `wp_update_term`. It also includes notice-logging so you can track what happens on a request.

This encapsulates save actions for this taxonomy in a set of methods, and gives you a place to override exactly _how_
this taxonomy is saved, should you decide to extend the class.

```php
underpin()->taxonomies()->get( 'taxonomy' )->save( [/* see wp_insert_post */] );
```

## Deleting Terms

This works in the same way as `save` and `query`. It includes logging, and provides a way to encapsulate the action.

```php
underpin()->taxonomies()->get( 'taxonomy' )->delete( $term );
```
