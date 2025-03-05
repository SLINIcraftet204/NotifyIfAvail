import PluginManager from 'src/plugin-system/plugin.manager';
import NotifyMe from './plugin/notify-me.plugin';

PluginManager.register('NotifyMe', NotifyMe, '[data-notify-me]');
