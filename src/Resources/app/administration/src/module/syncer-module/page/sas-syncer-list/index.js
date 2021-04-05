import { Component, Mixin, State, Service } from 'src/core/shopware';
import template from './sas-syncer-list.twig';

import './sas-syncer-list.scss';
Component.register('sas-syncer-list', {
    template,
    data() {
        return {
            token:null,
            url:null,
            res: null
        };
    },
    computed: {

        syncRestApiService() {
            return Service('syncRestApiService');
        }

    },
    methods: {
        onSave() {
            this.res = this.syncRestApiService.myCustomAction();
        }
    }
});
