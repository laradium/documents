!function(t){var e={};function n(i){if(e[i])return e[i].exports;var a=e[i]={i:i,l:!1,exports:{}};return t[i].call(a.exports,a,a.exports,n),a.l=!0,a.exports}n.m=t,n.c=e,n.d=function(t,e,i){n.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:i})},n.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var i=Object.create(null);if(n.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var a in t)n.d(i,a,function(e){return t[e]}.bind(null,a));return i},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="/",n(n.s=6)}([function(t,e,n){"use strict";function i(t,e,n,i,a,o,s,l){var r,d="function"==typeof t?t.options:t;if(e&&(d.render=e,d.staticRenderFns=n,d._compiled=!0),i&&(d.functional=!0),o&&(d._scopeId="data-v-"+o),s?(r=function(t){(t=t||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext)||"undefined"==typeof __VUE_SSR_CONTEXT__||(t=__VUE_SSR_CONTEXT__),a&&a.call(this,t),t&&t._registeredComponents&&t._registeredComponents.add(s)},d._ssrRegister=r):a&&(r=l?function(){a.call(this,this.$root.$options.shadowRoot)}:a),r)if(d.functional){d._injectStyles=r;var u=d.render;d.render=function(t,e){return r.call(e),u(t,e)}}else{var c=d.beforeCreate;d.beforeCreate=c?[].concat(c,r):[r]}return{exports:t,options:d}}n.d(e,"a",function(){return i})},function(t,e,n){"use strict";n.r(e);var i={props:["field","inline"],data:function(){return{id:Math.random().toString(36).substr(2,9),content:{label:null,name:"custom-content",config:{is_translatable:!1},value:this.field.value}}},methods:{saveContent:function(){this.field.value=this.content.value}}},a=n(0),o=Object(a.a)(i,function(){var t=this,e=t.$createElement,n=t._self._c||e;return t.field.config.exists?n("div",{class:{"d-inline-block":this.inline}},[n("input",{attrs:{type:"hidden",name:t.field.name},domProps:{value:t.field.value}}),t._v(" "),n("a",t._b({staticClass:"btn btn-primary",attrs:{href:"#edit-document"+this.id,"data-toggle":"modal"}},"a",t.fieldAttributes,!1),[t.field.label?[t._v("\n            "+t._s(t.field.label)+"\n        ")]:[n("i",{staticClass:"fa fa-pencil"}),t._v(" Edit\n        ")]],2),t._v(" "),n("div",{staticClass:"modal",attrs:{id:"edit-document"+this.id,tabindex:"-1",role:"dialog"}},[n("div",{staticClass:"modal-dialog modal-lg",attrs:{role:"document"}},[n("div",{staticClass:"modal-content"},[t._m(0),t._v(" "),n("div",{staticClass:"modal-body"},[n("wysiwyg-field",{attrs:{field:t.content}})],1),t._v(" "),n("div",{staticClass:"modal-footer"},[n("button",{staticClass:"btn btn-primary",attrs:{type:"button","data-dismiss":"modal"},on:{click:t.saveContent}},[t._v("\n                        Save\n                    ")]),t._v(" "),n("button",{staticClass:"btn btn-secondary",attrs:{type:"button","data-dismiss":"modal"}},[t._v("\n                        Close\n                    ")])])])])])]):t._e()},[function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"modal-header"},[e("h5",{staticClass:"modal-title"},[this._v("Edit document")]),this._v(" "),e("button",{staticClass:"close",attrs:{type:"button","data-dismiss":"modal","aria-label":"Close"}},[e("span",{attrs:{"aria-hidden":"true"}},[this._v("×")])])])}],!1,null,null,null);e.default=o.exports},,,,,function(t,e,n){t.exports=n(7)},function(t,e,n){void 0===window.laradiumFields&&(window.laradiumFields={}),window.laradiumFields.DownloadDocument=n(8).default,window.laradiumFields.EditDocument=n(1).default},function(t,e,n){"use strict";n.r(e);var i={props:["field"],components:{EditDocument:n(1).default}},a=n(0),o=Object(a.a)(i,function(){var t=this,e=t.$createElement,n=t._self._c||e;return t.field.config.exists?n("div",[n("a",t._b({staticClass:"btn btn-primary",attrs:{href:t.field.value}},"a",t.fieldAttributes,!1),[t.field.label?[t._v("\n            "+t._s(t.field.label)+"\n        ")]:[n("i",{staticClass:"fa fa-download"}),t._v(" Download\n        ")]],2),t._v(" "),t.field.config.with_edit?[n("edit-document",{attrs:{field:t.field.edit_field,inline:!0}})]:t._e()],2):t._e()},[],!1,null,null,null);e.default=o.exports}]);