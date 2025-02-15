# Play Pulse Panel Plugin System

This directory contains the plugin system for the Play Pulse Panel, allowing developers to extend and customize the panel's functionality.

## Plugin Structure

A typical plugin should follow this structure:

```
plugin-name/
├── config/
│   └── plugin-name.php
├── database/
│   └── migrations/
├── resources/
│   └── views/
├── routes/
│   └── web.php
├── src/
│   ├── Http/
│   │   └── Controllers/
│   └── PluginServiceProvider.php
└── config.json
```

## Creating a Plugin

1. Create a new directory in the `plugins` folder with your plugin name
2. Create a `config.json` file with plugin metadata
3. Implement your plugin's ServiceProvider
4. Add routes, controllers, and views as needed

## Plugin Configuration

The `config.json` file must contain:

```json
{
	"name": "plugin-name",
	"version": "1.0.0",
	"author": "Your Name",
	"type": "integration|theme|game|authentication|backup|monitoring",
	"description": "Plugin description",
	"minimum_panel_version": "1.0.0",
	"providers": [
		"Your\\Plugin\\ServiceProvider"
	]
}
```

## Plugin Management

Use the following commands to manage plugins:

```bash
# Install a plugin
php artisan plugin install <plugin-name>

# Uninstall a plugin
php artisan plugin uninstall <plugin-name>

# Update a plugin
php artisan plugin update <plugin-name>

# List installed plugins
php artisan plugin list
```

## Development Guidelines

1. Follow Laravel best practices
2. Use dependency injection
3. Write clean, documented code
4. Include proper error handling
5. Add appropriate security measures
6. Test thoroughly before distribution

## Example Plugin

See the `example-plugin` directory for a complete working example of a plugin implementation.