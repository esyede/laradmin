(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-100690bc"],{"0926":function(t,e,n){"use strict";n("13de")},"0e03":function(t,e,n){"use strict";var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return t.page?n("a-pagination",t._b({ref:"page",staticClass:"my-1 pagination",attrs:{"page-size-options":t.pageSizes,total:t.page.total,"show-size-changer":t.showSizeChanger,"show-quick-jumper":t.showQuickJumper,"show-total":function(t){return t+" total"},"page-size":t.perPage},on:{"update:pageSize":function(e){t.perPage=e},"update:page-size":function(e){t.perPage=e},change:t.onChange,showSizeChange:t.onSizeChange},model:{value:t.currentPage,callback:function(e){t.currentPage=e},expression:"currentPage"}},"a-pagination",t.$attrs,!1)):t._e()},r=[],i=(n("d401"),n("0d03"),n("d3b7"),n("25f0"),n("c975"),n("99af"),{name:"LzPagination",data:function(){return{currentPage:1,perPage:15}},props:{page:Object,autoPush:{type:Boolean,default:!0},showSizeChanger:{type:Boolean,default:!0},showQuickJumper:{type:Boolean,default:!0}},computed:{pageSizes:function(){var t=["15","30","50","100","200"],e=this.page.per_page.toString();return-1===t.indexOf(e)?[e].concat(t):t}},methods:{push:function(t){var e=t.page,n=t.perPage,a=Object.assign({},this.$route.query,{page:e||this.currentPage,per_page:n||this.perPage});this.$router.push({path:this.$route.path,query:a})},onSizeChange:function(t,e){this.$emit("size-change",e),this.autoPush&&(this.currentPage=1,this.push({page:1,perPage:e}))},onChange:function(t){this.$emit("current-change",t),this.autoPush&&this.push({page:t})}},watch:{page:{handler:function(t){t&&(this.currentPage=t.current_page,this.perPage=t.per_page)},immediate:!0}}}),o=i,c=(n("12a8"),n("2877")),s=Object(c["a"])(o,a,r,!1,null,"62cdeb6b",null);e["a"]=s.exports},1240:function(t,e,n){},"12a8":function(t,e,n){"use strict";n("1240")},"13de":function(t,e,n){},"2aad":function(t,e,n){"use strict";n.r(e);var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("page-content",[n("space",{staticClass:"my-1"},[n("search-form",{attrs:{fields:t.search}})],1),n("a-table",{attrs:{"row-key":"id","data-source":t.perms,bordered:"",scroll:{x:1200},pagination:!1}},[n("a-table-column",{attrs:{title:"ID","data-index":"id",width:60}}),n("a-table-column",{attrs:{title:"Name","data-index":"name",width:150}}),n("a-table-column",{attrs:{title:"Slug","data-index":"slug",width:150}}),n("a-table-column",{attrs:{title:"Data"},scopedSlots:t._u([{key:"default",fn:function(t){return[n("route-show",{attrs:{data:t}})]}}])}),n("a-table-column",{attrs:{title:"Created At","data-index":"created_at",width:180}}),n("a-table-column",{attrs:{title:"Updated At","data-index":"updated_at",width:180}}),n("a-table-column",{attrs:{title:"Actions",width:100},scopedSlots:t._u([{key:"default",fn:function(e){return[n("space",[n("router-link",{attrs:{to:"/admin-permissions/"+e.id+"/edit"}},[t._v("Edit")]),n("lz-popconfirm",{attrs:{confirm:t.destroyAdminPerm(e.id)}},[n("a",{staticClass:"error-color",attrs:{href:"javascript:void(0);"}},[t._v("Delete")])])],1)]}}])})],1),n("lz-pagination",{attrs:{page:t.page}})],1)},r=[],i=n("1da1"),o=(n("96cf"),n("cfb6")),c=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",t._l(t.httpRoute,(function(e,a){return n("div",{key:a,staticClass:"mb-1"},[t._l(e.method,(function(e){return n("a-tag",{key:e,staticClass:"mr-1",attrs:{color:"blue"}},[t._v(" "+t._s(e)+" ")])})),n("code",[t._v(t._s("/admin-api"+e.path))])],2)})),0)},s=[],u=n("3835"),l=(n("d81d"),n("c975"),n("4de4"),n("d3b7"),n("ac1f"),n("1276"),{name:"RouteShow",props:{data:Object},computed:{httpRoute:function(){var t=this,e=this.data.http_path;return e?e.map((function(e){var n=t.data.http_method,a=e;if(-1!==e.indexOf(":")){var r=e.split(":"),i=Object(u["a"])(r,2);n=i[0],a=i[1],n=n.split(",").filter((function(t){return!!t}))}return 0===n.length&&(n=["ANY"]),{method:n,path:a}})):[]}}}),d=l,f=n("2877"),p=Object(f["a"])(d,c,s,!1,null,null,null),h=p.exports,m=n("1977"),b=n("0e03"),g=n("c001"),v=n("7e65"),y=n("eff2"),w=n("f771"),_={name:"Index",scroll:!0,components:{LzPopconfirm:y["a"],PageContent:g["a"],LzPagination:b["a"],Space:m["a"],SearchForm:v["a"],RouteShow:h},data:function(){return{perms:[],page:null,search:[{field:"id",label:"ID"},{field:"name",label:"Name"},{field:"slug",label:"Slug"},{field:"http_path",label:"Path"}]}},methods:{destroyAdminPerm:function(t){var e=this;return Object(i["a"])(regeneratorRuntime.mark((function n(){return regeneratorRuntime.wrap((function(n){while(1)switch(n.prev=n.next){case 0:return n.next=2,Object(o["a"])(t);case 2:e.perms=Object(w["r"])(e.perms,(function(e){return e.id===t}));case 3:case"end":return n.stop()}}),n)})))}},watch:{$route:{handler:function(t){var e=this;return Object(i["a"])(regeneratorRuntime.mark((function n(){var a,r,i,c;return regeneratorRuntime.wrap((function(n){while(1)switch(n.prev=n.next){case 0:return n.next=2,Object(o["c"])(t.query);case 2:a=n.sent,r=a.data,i=r.data,c=r.meta,e.perms=i,e.page=c,e.$scrollResolve();case 9:case"end":return n.stop()}}),n)})))()},immediate:!0}}},x=_,k=Object(f["a"])(x,a,r,!1,null,null,null);e["default"]=k.exports},3835:function(t,e,n){"use strict";n.d(e,"a",(function(){return c}));n("277d");function a(t){if(Array.isArray(t))return t}n("a4d3"),n("e01a"),n("d3b7"),n("d28b"),n("3ca3"),n("ddb0");function r(t,e){var n=null==t?null:"undefined"!==typeof Symbol&&t[Symbol.iterator]||t["@@iterator"];if(null!=n){var a,r,i=[],o=!0,c=!1;try{for(n=n.call(t);!(o=(a=n.next()).done);o=!0)if(i.push(a.value),e&&i.length===e)break}catch(s){c=!0,r=s}finally{try{o||null==n["return"]||n["return"]()}finally{if(c)throw r}}return i}}var i=n("06c5");n("d9e2"),n("d401");function o(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}function c(t,e){return a(t)||r(t,e)||Object(i["a"])(t,e)||o()}},"498a":function(t,e,n){"use strict";var a=n("23e7"),r=n("58a8").trim,i=n("c8d2");a({target:"String",proto:!0,forced:i("trim")},{trim:function(){return r(this)}})},"56ba":function(t,e,n){"use strict";n("a128")},"7e65":function(t,e,n){"use strict";var a,r,i=n("5530"),o=n("1da1"),c=(n("96cf"),n("45fc"),n("d3b7"),n("4160"),n("159b"),n("498a"),n("b0c0"),n("d81d"),n("1977")),s={name:"SearchForm",components:{Space:c["a"]},data:function(){return{form:{}}},computed:{anyQuery:function(){var t=this;return this.fields.some((function(e){if(t.$route.query[e.field])return!0}))}},props:{fields:Array,resetCurrentPage:{type:Boolean,default:!0},maxWidth:{type:String,default:"700px"}},methods:{onSubmit:function(){var t=this;return Object(o["a"])(regeneratorRuntime.mark((function e(){var n;return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:return n=Object(i["a"])({},t.$route.query),t.resetCurrentPage&&delete n.page,t.fields.forEach((function(e){var a=e.field,r=t.form[a];"string"===typeof r&&(r=r.trim()),""===r||void 0===r?delete n[a]:n[a]=r})),e.prev=3,e.next=6,t.$router.push({path:t.$route.path,query:n});case 6:e.next=12;break;case 8:if(e.prev=8,e.t0=e["catch"](3),"NavigationDuplicated"===e.t0.name){e.next=12;break}throw e.t0;case 12:case"end":return e.stop()}}),e,null,[[3,8]])})))()},onReset:function(){this.form={},this.onSubmit()},setFormValueFromQuery:function(){var t=this,e=this.$route.query;this.fields.forEach((function(n){var a=n.field,r=e[a];t.$set(t.form,a,r)}))}},watch:{$route:{handler:function(){this.setFormValueFromQuery()},immediate:!0}},render:function(t){var e=this;return t("a-popover",{attrs:{trigger:"click",placement:"bottomLeft","overlay-style":{padding:"10px",maxWidth:this.maxWidth}}},[t("template",{slot:"content"},[t("a-form",{attrs:{layout:"inline"},nativeOn:{keydown:function(t){return!("button"in t)&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:(t.preventDefault(),e.onSubmit(t))}}},[this.fields.map((function(n){var a;switch(n.type){case"select":a=t("a-select",{attrs:{"default-valut":e.form[n.field],placeholder:n.label,"allow-clear":!0,"show-search":!0},model:{value:e.form[n.field],callback:function(t){e.$set(e.form,n.field,t)}}},[n.options.map((function(e){var a=String(e[n.valueField||"id"]),r=e[n.labelField||"name"];return t("a-select-option",{attrs:{value:a}},[r])}))]);break;case"input":default:a=t("a-input",{attrs:{placeholder:n.label,"allow-clear":!0},model:{value:e.form[n.field],callback:function(t){e.$set(e.form,n.field,t)}}})}return t("a-form-item",{key:n.field},[a])})),t("a-form-item",{class:"actions"},[t("space",[t("a-button",{attrs:{type:"primary"},on:{click:this.onSubmit}},["Search"]),t("a-button",{on:{click:this.onReset}},["Reset"])])])])]),t("a-button",{attrs:{type:this.anyQuery?"primary":""}},["Search"])])}},u=s,l=(n("56ba"),n("2877")),d=Object(l["a"])(u,a,r,!1,null,"397b2f70",null);e["a"]=d.exports},a128:function(t,e,n){},c001:function(t,e,n){"use strict";var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("div",{staticClass:"content-header"},[n("span",{staticClass:"title"},[t._v(t._s(t.realName))]),n("div",{staticClass:"flex-spacer"}),n("div",[t._t("actions")],2)]),n("div",{class:{center:t.center}},[t._t("default")],2)])},r=[],i=n("5530"),o=n("2f62"),c=n("4416"),s=n.n(c),u={name:"PageContent",props:{title:String,center:Boolean},computed:Object(i["a"])(Object(i["a"])({},Object(o["c"])({matchedMenusChain:function(t){return t.matchedMenusChain}})),{},{realName:function(){var t,e;if(this.title)return this.title;var n="";return this.matchedMenusChain.length&&(n=s()(this.matchedMenusChain).title),n||(null===(t=this.$route)||void 0===t||null===(e=t.meta)||void 0===e?void 0:e.title)||""}})},l=u,d=(n("c402"),n("2877")),f=Object(d["a"])(l,a,r,!1,null,"4e857efe",null);e["a"]=f.exports},c402:function(t,e,n){"use strict";n("cf86")},c8d2:function(t,e,n){var a=n("5e77").PROPER,r=n("d039"),i=n("5899"),o="​᠎";t.exports=function(t){return r((function(){return!!i[t]()||o[t]()!==o||a&&i[t].name!==t}))}},cf86:function(t,e,n){},cfb6:function(t,e,n){"use strict";n.d(e,"c",(function(){return r})),n.d(e,"d",(function(){return i})),n.d(e,"a",(function(){return o})),n.d(e,"e",(function(){return c})),n.d(e,"b",(function(){return s}));var a=n("f3e5");function r(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return a["b"].get("admin-permissions",{params:t})}function i(t){return a["b"].post("admin-permissions",t)}function o(t){return a["b"]["delete"]("admin-permissions/".concat(t))}function c(t,e){return a["b"].put("admin-permissions/".concat(t),e)}function s(t){return a["b"].get("admin-permissions/".concat(t,"/edit"))}},eff2:function(t,e,n){"use strict";var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("a-popover",t._b({attrs:{trigger:"click",visible:t.visible},on:{visibleChange:t.onVisibleChange},scopedSlots:t._u([{key:"content",fn:function(){return[n("div",{staticClass:"mb-2"},[t.title?n("span",[t._v(t._s(t.title))]):t._t("title")],2),n("space",{staticClass:"actions"},[n("a-button",{attrs:{size:"small"},on:{click:t.onCancel}},[t._v(t._s(t.cancelText))]),n("loading-action",{attrs:{action:t.onConfirm,size:"small",type:"primary"}},[t._v(" "+t._s(t.okText)+" ")])],1)]},proxy:!0}])},"a-popover",t.$attrs,!1),[t._t("default",null,{pop:this})],2)},r=[],i=n("1da1"),o=(n("96cf"),n("1977")),c=n("e6bb"),s={name:"LzPopconfirm",components:{LoadingAction:c["a"],Space:o["a"]},data:function(){return{visible:!1}},props:{title:{type:String,default:"Are you sure?"},cancelText:{type:String,default:"Cancel"},okText:{type:String,default:"OK"},confirm:Function,disabled:Boolean},methods:{onCancel:function(){this.visible=!1,this.$emit("cancel")},onConfirm:function(){var t=this;return Object(i["a"])(regeneratorRuntime.mark((function e(){return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:if(e.t0=t.confirm,!e.t0){e.next=4;break}return e.next=4,t.confirm();case 4:t.$emit("confirm"),t.visible=!1;case 6:case"end":return e.stop()}}),e)})))()},onVisibleChange:function(t){this.disabled||(this.visible=t)}}},u=s,l=(n("0926"),n("2877")),d=Object(l["a"])(u,a,r,!1,null,"cd721546",null);e["a"]=d.exports}}]);
//# sourceMappingURL=chunk-100690bc.5d37caa3.js.map