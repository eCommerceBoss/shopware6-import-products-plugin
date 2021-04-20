// Import all necessary Storefront plugins
import ConfigOptionPlugin from './configoption-plugin/configoption-plugin.plugin';

// Register your plugin via the existing PluginManager
const PluginManager = window.PluginManager;
PluginManager.register('ConfigOptionPlugin', ConfigOptionPlugin, '[data-configoption-plugin]');