
import ApiService from 'src/core/service/api.service';
class SyncRestApiService extends ApiService 
{
    constructor(httpClient, loginService, apiEndpoint = 'sas-syncer') {
        super(httpClient, loginService, apiEndpoint);
        this.name = 'SyncRestApiService';
    }

    myCustomAction() {
        return this.httpClient
            .get(`v1/${this.getApiBasePath()}/my-api-action`, {
                headers: this.getBasicHeaders()
            })
            .then((response) => {
                return ApiService.handleResponse(response);
            });
    }
}

export default SyncRestApiService;