const Application = Shopware.Application;
import SyncRestApiService from '../services/sync.service';
Shopware.Service().register('syncRestApiService', (container) => {
    const initContainer = Shopware.Application.getContainer('init');
    return new SyncRestApiService(initContainer.httpClient, Shopware.Service('loginService'));
});