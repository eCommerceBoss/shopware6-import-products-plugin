import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';

export default class ConfigOptionPlugin extends Plugin {
	static options = {
        configOptionUrl: '/store-api/v3/context'
    };

	init() {
		document.getElementById("configurator").firstElementChild.addEventListener("change", this.onChange.bind(this));
    	this._httpClient = new HttpClient();
    }

    onChange(event) {
        const data = {};
    	var optionId = event.target.value;
        if(optionId != "")
        {
            data._csrf_token = document.querySelector('#configurator > input[name=_csrf_token]').value;
            data.option = optionId;

            this._httpClient.post("/config", JSON.stringify(data), (response) => {
                console.log(response);
            });

            //save optionid into cookie
        }
        
    }
}