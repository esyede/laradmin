(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-165c9550"],{"7ac6":function(e,n,t){"use strict";t.r(n);var r=function(){var e=this,n=e.$createElement,t=e._self._c||n;return t("div",{staticClass:"login"},[t("a-card",{staticClass:"login-card",attrs:{title:e.appName}},[t("login-form",{ref:"form",nativeOn:{keydown:function(n){return!n.type.indexOf("key")&&e._k(n.keyCode,"enter",13,n.key,"Enter")?null:e.$refs.login.onAction.apply(null,arguments)}}}),t("loading-action",{ref:"login",staticClass:"w-100",attrs:{type:"primary",action:e.onLogin,"disable-on-success":"2000"}},[t("span",[e._v("Login")])])],1)],1)},a=[],o=t("1da1"),i=(t("96cf"),t("d3b7"),t("0199")),c=t("e6bb"),s=t("8c4f"),u={name:"Login",components:{LoadingAction:c["a"],LoginForm:i["a"]},computed:{appName:function(){return this.$store.getters.appName}},methods:{onLogin:function(){var e=this;return Object(o["a"])(regeneratorRuntime.mark((function n(){return regeneratorRuntime.wrap((function(n){while(1)switch(n.prev=n.next){case 0:return n.next=2,e.$refs.form.onSubmit();case 2:e.$router.push(e.$route.query.redirect||"/")["catch"]((function(e){s["a"].isNavigationFailure(e)||Promise.reject(e)}));case 3:case"end":return n.stop()}}),n)})))()}}},l=u,p=(t("ca37"),t("2877")),f=Object(p["a"])(l,r,a,!1,null,"dad0d064",null);n["default"]=f.exports},b990:function(e,n,t){},ca37:function(e,n,t){"use strict";t("b990")}}]);
//# sourceMappingURL=chunk-165c9550.f0fe3b79.js.map