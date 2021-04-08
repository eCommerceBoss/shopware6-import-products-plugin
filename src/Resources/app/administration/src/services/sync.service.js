
import ApiService from 'src/core/service/api.service';
class SyncRestApiService extends ApiService 
{
    constructor(httpClient, loginService, apiEndpoint = 'v1/sas-syncer') {
        super(httpClient, loginService, apiEndpoint);
        this.name = 'SyncRestApiService';
    }

    myCustomAction() {
        return this.httpClient
            .get(`${this.getApiBasePath()}/my-action-api`, {
                headers: this.getBasicHeaders()
            })
            .then((response) => {
                console.log(response);
                return ApiService.handleResponse(response.data);
            });
    }
}

export default SyncRestApiService;