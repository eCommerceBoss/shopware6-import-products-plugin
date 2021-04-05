import { Module } from 'src/core/shopware';

import './page/sas-syncer-list';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

Module.register('syncer-module', {
    type: 'plugin',
    name: 'Syncer',
    title: 'sas-syncer.general.mainMenuItemGeneral',
    description: 'sas-syncer.general.descriptionTextModule',
    color: '#62ff80',
    icon: 'default-object-lab-flask',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        index: {
            components: {
                default: 'sas-syncer-list'
            },
            path: 'index',
        }
    },

    navigation: [
        {
            id: 'sas-syncer',
            label: 'sas-syncer.general.mainMenuItemGeneral',
            path: 'syncer.module.index',
            parent: 'sw-content',
        }
    ]
});
