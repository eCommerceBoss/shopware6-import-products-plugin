import { Component, Mixin, State, Service } from 'src/core/shopware';
import template from './sas-syncer-list.twig';

import './sas-syncer-list.scss';
Component.register('sas-syncer-list', {
    template,
    data() {
        return {
            token:null,
            url:null,
            res: null,
            isLoading:false,
            result:[]
        };
    },
    computed: {

        syncRestApiService() {
            return Service('syncRestApiService');
        }

    },
    methods: {
        async onSave() {
            this.isLoading = true;
            const response = await this.syncRestApiService.myCustomAction();
            this.isLoading = false;
            for(var i = 0; i < response.length; i++)
            {
                this.result[i] = (i+1) + '. ' + response[i].name;
            }

        }
    }
});
