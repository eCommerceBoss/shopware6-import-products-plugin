(window.webpackJsonp=window.webpackJsonp||[]).push([["sas-syncer-module"],{"6OZB":function(t,n,e){"use strict";e.r(n);var o=e("FGIj"),r=e("k8s9");function i(t){return(i="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function u(t,n){if(!(t instanceof n))throw new TypeError("Cannot call a class as a function")}function c(t,n){for(var e=0;e<n.length;e++){var o=n[e];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}function a(t,n){return!n||"object"!==i(n)&&"function"!=typeof n?function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t):n}function f(t){return(f=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}function l(t,n){return(l=Object.setPrototypeOf||function(t,n){return t.__proto__=n,t})(t,n)}var p,s,y,b=function(t){function n(){return u(this,n),a(this,f(n).apply(this,arguments))}var e,o,i;return function(t,n){if("function"!=typeof n&&null!==n)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(n&&n.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),n&&l(t,n)}(n,t),e=n,(o=[{key:"init",value:function(){document.getElementById("configurator").firstElementChild.addEventListener("change",this.onChange.bind(this)),this._httpClient=new r.a}},{key:"onChange",value:function(t){var n={},e=t.target.value;""!=e&&(n._csrf_token=document.querySelector("#configurator > input[name=_csrf_token]").value,n.option=e,this._httpClient.post("/config",JSON.stringify(n),(function(t){console.log(t)})))}}])&&c(e.prototype,o),i&&c(e,i),n}(o.a);y={configOptionUrl:"/store-api/v3/context"},(s="options")in(p=b)?Object.defineProperty(p,s,{value:y,enumerable:!0,configurable:!0,writable:!0}):p[s]=y,window.PluginManager.register("ConfigOptionPlugin",b,"[data-configoption-plugin]")}},[["6OZB","runtime","vendor-node","vendor-shared"]]]);