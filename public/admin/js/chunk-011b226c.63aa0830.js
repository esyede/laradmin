(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-011b226c"],{"0926":function(e,t,a){"use strict";a("13de")},"0e03":function(e,t,a){"use strict";var n=function(){var e=this,t=e.$createElement,a=e._self._c||t;return e.page?a("a-pagination",e._b({ref:"page",staticClass:"my-1 pagination",attrs:{"page-size-options":e.pageSizes,total:e.page.total,"show-size-changer":e.showSizeChanger,"show-quick-jumper":e.showQuickJumper,"show-total":function(e){return e+" total"},"page-size":e.perPage},on:{"update:pageSize":function(t){e.perPage=t},"update:page-size":function(t){e.perPage=t},change:e.onChange,showSizeChange:e.onSizeChange},model:{value:e.currentPage,callback:function(t){e.currentPage=t},expression:"currentPage"}},"a-pagination",e.$attrs,!1)):e._e()},r=[],i=(a("d401"),a("0d03"),a("d3b7"),a("25f0"),a("c975"),a("99af"),{name:"LzPagination",data:function(){return{currentPage:1,perPage:15}},props:{page:Object,autoPush:{type:Boolean,default:!0},showSizeChanger:{type:Boolean,default:!0},showQuickJumper:{type:Boolean,default:!0}},computed:{pageSizes:function(){var e=["15","30","50","100","200"],t=this.page.per_page.toString();return-1===e.indexOf(t)?[t].concat(e):e}},methods:{push:function(e){var t=e.page,a=e.perPage,n=Object.assign({},this.$route.query,{page:t||this.currentPage,per_page:a||this.perPage});this.$router.push({path:this.$route.path,query:n})},onSizeChange:function(e,t){this.$emit("size-change",t),this.autoPush&&(this.currentPage=1,this.push({page:1,perPage:t}))},onChange:function(e){this.$emit("current-change",e),this.autoPush&&this.push({page:e})}},watch:{page:{handler:function(e){e&&(this.currentPage=e.current_page,this.perPage=e.per_page)},immediate:!0}}}),o=i,s=(a("12a8"),a("2877")),c=Object(s["a"])(o,n,r,!1,null,"62cdeb6b",null);t["a"]=c.exports},1240:function(e,t,a){},"12a8":function(e,t,a){"use strict";a("1240")},"13de":function(e,t,a){},"498a":function(e,t,a){"use strict";var n=a("23e7"),r=a("58a8").trim,i=a("c8d2");n({target:"String",proto:!0,forced:i("trim")},{trim:function(){return r(this)}})},"56ba":function(e,t,a){"use strict";a("a128")},"7e65":function(e,t,a){"use strict";var n,r,i=a("5530"),o=a("1da1"),s=(a("96cf"),a("45fc"),a("d3b7"),a("4160"),a("159b"),a("498a"),a("b0c0"),a("d81d"),a("1977")),c={name:"SearchForm",components:{Space:s["a"]},data:function(){return{form:{}}},computed:{anyQuery:function(){var e=this;return this.fields.some((function(t){if(e.$route.query[t.field])return!0}))}},props:{fields:Array,resetCurrentPage:{type:Boolean,default:!0},maxWidth:{type:String,default:"700px"}},methods:{onSubmit:function(){var e=this;return Object(o["a"])(regeneratorRuntime.mark((function t(){var a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return a=Object(i["a"])({},e.$route.query),e.resetCurrentPage&&delete a.page,e.fields.forEach((function(t){var n=t.field,r=e.form[n];"string"===typeof r&&(r=r.trim()),""===r||void 0===r?delete a[n]:a[n]=r})),t.prev=3,t.next=6,e.$router.push({path:e.$route.path,query:a});case 6:t.next=12;break;case 8:if(t.prev=8,t.t0=t["catch"](3),"NavigationDuplicated"===t.t0.name){t.next=12;break}throw t.t0;case 12:case"end":return t.stop()}}),t,null,[[3,8]])})))()},onReset:function(){this.form={},this.onSubmit()},setFormValueFromQuery:function(){var e=this,t=this.$route.query;this.fields.forEach((function(a){var n=a.field,r=t[n];e.$set(e.form,n,r)}))}},watch:{$route:{handler:function(){this.setFormValueFromQuery()},immediate:!0}},render:function(e){var t=this;return e("a-popover",{attrs:{trigger:"click",placement:"bottomLeft","overlay-style":{padding:"10px",maxWidth:this.maxWidth}}},[e("template",{slot:"content"},[e("a-form",{attrs:{layout:"inline"},nativeOn:{keydown:function(e){return!("button"in e)&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:(e.preventDefault(),t.onSubmit(e))}}},[this.fields.map((function(a){var n;switch(a.type){case"select":n=e("a-select",{attrs:{"default-valut":t.form[a.field],placeholder:a.label,"allow-clear":!0,"show-search":!0},model:{value:t.form[a.field],callback:function(e){t.$set(t.form,a.field,e)}}},[a.options.map((function(t){var n=String(t[a.valueField||"id"]),r=t[a.labelField||"name"];return e("a-select-option",{attrs:{value:n}},[r])}))]);break;case"input":default:n=e("a-input",{attrs:{placeholder:a.label,"allow-clear":!0},model:{value:t.form[a.field],callback:function(e){t.$set(t.form,a.field,e)}}})}return e("a-form-item",{key:a.field},[n])})),e("a-form-item",{class:"actions"},[e("space",[e("a-button",{attrs:{type:"primary"},on:{click:this.onSubmit}},["Search"]),e("a-button",{on:{click:this.onReset}},["Reset"])])])])]),e("a-button",{attrs:{type:this.anyQuery?"primary":""}},["Search"])])}},u=c,l=(a("56ba"),a("2877")),f=Object(l["a"])(u,n,r,!1,null,"397b2f70",null);t["a"]=f.exports},a128:function(e,t,a){},c001:function(e,t,a){"use strict";var n=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",[a("div",{staticClass:"content-header"},[a("span",{staticClass:"title"},[e._v(e._s(e.realName))]),a("div",{staticClass:"flex-spacer"}),a("div",[e._t("actions")],2)]),a("div",{class:{center:e.center}},[e._t("default")],2)])},r=[],i=a("5530"),o=a("2f62"),s=a("4416"),c=a.n(s),u={name:"PageContent",props:{title:String,center:Boolean},computed:Object(i["a"])(Object(i["a"])({},Object(o["c"])({matchedMenusChain:function(e){return e.matchedMenusChain}})),{},{realName:function(){var e,t;if(this.title)return this.title;var a="";return this.matchedMenusChain.length&&(a=c()(this.matchedMenusChain).title),a||(null===(e=this.$route)||void 0===e||null===(t=e.meta)||void 0===t?void 0:t.title)||""}})},l=u,f=(a("c402"),a("2877")),d=Object(f["a"])(l,n,r,!1,null,"4e857efe",null);t["a"]=d.exports},c402:function(e,t,a){"use strict";a("cf86")},c438:function(e,t,a){"use strict";a.r(t);var n=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("page-content",[a("space",{staticClass:"my-1"},[a("search-form",{attrs:{fields:e.search}})],1),a("a-table",{attrs:{"row-key":"id","data-source":e.users,bordered:"",scroll:{x:1500},pagination:!1}},[a("a-table-column",{attrs:{title:"ID","data-index":"id",width:60}}),a("a-table-column",{attrs:{title:"Fullname","data-index":"name",width:150}}),a("a-table-column",{attrs:{title:"Username","data-index":"username",width:200}}),a("a-table-column",{attrs:{title:"Roles"},scopedSlots:e._u([{key:"default",fn:function(t){return e._l(t.roles,(function(t){return a("a-tag",{key:t,attrs:{color:"blue"}},[e._v(e._s(t))])}))}}])}),a("a-table-column",{attrs:{title:"Permission"},scopedSlots:e._u([{key:"default",fn:function(t){return e._l(t.permissions,(function(t){return a("a-tag",{key:t,attrs:{color:"blue"}},[e._v(e._s(t))])}))}}])}),a("a-table-column",{attrs:{title:"Created At","data-index":"created_at",width:180}}),a("a-table-column",{attrs:{title:"Updated At","data-index":"updated_at",width:180}}),a("a-table-column",{attrs:{title:"Actions",width:100},scopedSlots:e._u([{key:"default",fn:function(t){return[a("space",[a("router-link",{attrs:{to:"/admin-users/"+t.id+"/edit"}},[e._v("Edit")]),a("lz-popconfirm",{attrs:{confirm:e.destroyAdminRole(t.id)}},[a("a",{staticClass:"error-color",attrs:{href:"javascript:void(0);"}},[e._v("Delete")])])],1)]}}])})],1),a("lz-pagination",{attrs:{page:e.page}})],1)},r=[],i=a("1da1"),o=(a("96cf"),a("3a02")),s=a("1977"),c=a("0e03"),u=a("c001"),l=a("7e65"),f=a("eff2"),d=a("f771"),p={name:"Index",scroll:!0,components:{LzPopconfirm:f["a"],PageContent:u["a"],LzPagination:c["a"],Space:s["a"],SearchForm:l["a"]},data:function(){return{users:[],page:null,search:[{field:"id",label:"ID"},{field:"name",label:"Fullname"},{field:"username",label:"Username"},{field:"role_name",label:"Role"},{field:"permission_name",label:"Permission"}]}},methods:{destroyAdminRole:function(e){var t=this;return Object(i["a"])(regeneratorRuntime.mark((function a(){return regeneratorRuntime.wrap((function(a){while(1)switch(a.prev=a.next){case 0:return a.next=2,Object(o["b"])(e);case 2:t.users=Object(d["r"])(t.users,(function(t){return t.id===e}));case 3:case"end":return a.stop()}}),a)})))}},watch:{$route:{handler:function(e){var t=this;return Object(i["a"])(regeneratorRuntime.mark((function a(){var n,r,i,s;return regeneratorRuntime.wrap((function(a){while(1)switch(a.prev=a.next){case 0:return a.next=2,Object(o["e"])(e.query);case 2:n=a.sent,r=n.data,i=r.data,s=r.meta,t.users=i,t.page=s,t.$scrollResolve();case 9:case"end":return a.stop()}}),a)})))()},immediate:!0}}},m=p,h=a("2877"),g=Object(h["a"])(m,n,r,!1,null,null,null);t["default"]=g.exports},c8d2:function(e,t,a){var n=a("5e77").PROPER,r=a("d039"),i=a("5899"),o="​᠎";e.exports=function(e){return r((function(){return!!i[e]()||o[e]()!==o||n&&i[e].name!==e}))}},cf86:function(e,t,a){},eff2:function(e,t,a){"use strict";var n=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("a-popover",e._b({attrs:{trigger:"click",visible:e.visible},on:{visibleChange:e.onVisibleChange},scopedSlots:e._u([{key:"content",fn:function(){return[a("div",{staticClass:"mb-2"},[e.title?a("span",[e._v(e._s(e.title))]):e._t("title")],2),a("space",{staticClass:"actions"},[a("a-button",{attrs:{size:"small"},on:{click:e.onCancel}},[e._v(e._s(e.cancelText))]),a("loading-action",{attrs:{action:e.onConfirm,size:"small",type:"primary"}},[e._v(" "+e._s(e.okText)+" ")])],1)]},proxy:!0}])},"a-popover",e.$attrs,!1),[e._t("default",null,{pop:this})],2)},r=[],i=a("1da1"),o=(a("96cf"),a("1977")),s=a("e6bb"),c={name:"LzPopconfirm",components:{LoadingAction:s["a"],Space:o["a"]},data:function(){return{visible:!1}},props:{title:{type:String,default:"Are you sure?"},cancelText:{type:String,default:"Cancel"},okText:{type:String,default:"OK"},confirm:Function,disabled:Boolean},methods:{onCancel:function(){this.visible=!1,this.$emit("cancel")},onConfirm:function(){var e=this;return Object(i["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(t.t0=e.confirm,!t.t0){t.next=4;break}return t.next=4,e.confirm();case 4:e.$emit("confirm"),e.visible=!1;case 6:case"end":return t.stop()}}),t)})))()},onVisibleChange:function(e){this.disabled||(this.visible=e)}}},u=c,l=(a("0926"),a("2877")),f=Object(l["a"])(u,n,r,!1,null,"cd721546",null);t["a"]=f.exports}}]);
//# sourceMappingURL=chunk-011b226c.63aa0830.js.map