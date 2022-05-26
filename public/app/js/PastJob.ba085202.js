(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["PastJob"],{"7acb":function(t,e,s){"use strict";s.r(e);var a=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("main-layout",{attrs:{"is-show-header":!1}},[a("div",{staticClass:"box_page"},[a("div",{staticClass:"title_header"},[t._v("Past Jobs")]),a("div",{staticClass:"pt-4"},[a("div",{staticClass:"input_box"},[a("input",{directives:[{name:"model",rawName:"v-model",value:t.search_key,expression:"search_key"}],staticClass:"icon_input_search style_input1",attrs:{type:"text"},domProps:{value:t.search_key},on:{keyup:t.searchJob,input:function(e){e.target.composing||(t.search_key=e.target.value)}}}),a("img",{staticClass:"filter_icon",attrs:{src:s("2376"),alt:"icon-approved.svg"}}),a("img",{staticClass:"search_icon",attrs:{src:s("d103"),alt:""}})])]),a("div",{staticClass:"jobs_box"},t._l(t.jobs_search,(function(e){return a("div",{staticClass:"item_job",on:{click:function(s){return t.sheduleFile(e)}}},[a("div",{staticClass:"item_j"},[a("div",{staticClass:"box_i"},[a("div",{staticClass:"box_image"},[a("img",{directives:[{name:"lazy",rawName:"v-lazy",value:t.getJobImages(e)[0],expression:"getJobImages(job)[0]"}],staticClass:"job_image",attrs:{alt:"icon-approved.svg"}})]),a("div",{staticClass:"ms-2 box_i2"},[a("div",{staticClass:"description_item_j"},[e.style_guide?a("div",[a("span",{staticClass:"text_bold"},[t._v("Style Guide:")]),t._v(" "+t._s(e.style_guide)+" ")]):t._e(),a("div",[a("span",{staticClass:"text_bold"},[t._v("Files:")]),t._v(" "+t._s(t.getJobImages(e).length)+" ")]),a("div",[a("span",{staticClass:"text_bold"},[t._v("Editing Level:")]),t._v(" "+t._s(e.editing_level)+" ")]),a("div",[a("span",{staticClass:"text_bold"},[t._v("Acceptance:")]),t._v(" "+t._s(t.acceptance(e))+"% ")]),a("div",[a("span",{staticClass:"text_bold"},[t._v("Time Frame:")]),t._v(" "+t._s(t.timerFrame(e))+" min/avg ")])])])])])])})),0)])])},i=[],n=s("1da1"),o=s("5530"),r=(s("96cf"),s("d81d"),s("07ac"),s("4de4"),s("d3b7"),s("caad"),s("2532"),s("713b")),l=s("09f9"),c=s("2f62"),d=s("3703"),u={name:"PastJob",components:{MainLayout:r["a"]},data:function(){return{past_jobs:{},search_key:null,jobs_search:{},is_type_user:null,business:d["a"].BUSINESS,editor:d["a"].EDITOR}},mounted:function(){this.pastJobs()},methods:Object(o["a"])(Object(o["a"])({},Object(c["c"])(["showLoader","hideLoader"])),{},{getJobImages:function(t){return null!==t&&void 0!==t&&t.images?Object.values(null===t||void 0===t?void 0:t.images).map((function(t){var e;return null!==(e=t.src_cropped)&&void 0!==e?e:t.src})):[]},pastJobs:function(){var t=this;return Object(n["a"])(regeneratorRuntime.mark((function e(){var s,a,i,n,o,r,c,d,u,v,_,p;return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:return e.prev=0,t.showLoader(),e.next=4,t.$http.getAuth("".concat(t.$http.apiUrl(),"past-job"));case 4:d=e.sent,t.past_jobs=(null===d||void 0===d||null===(s=d.data)||void 0===s||null===(a=s.data)||void 0===a?void 0:a.past_jobs)||{},t.jobs_search=(null===d||void 0===d||null===(i=d.data)||void 0===i||null===(n=i.data)||void 0===n?void 0:n.past_jobs)||{},t.is_type_user=(null===d||void 0===d||null===(o=d.data)||void 0===o||null===(r=o.data)||void 0===r||null===(c=r.user)||void 0===c?void 0:c.type_user)||null,e.next=14;break;case 10:e.prev=10,e.t0=e["catch"](0),p=(null===e.t0||void 0===e.t0||null===(u=e.t0.response)||void 0===u||null===(v=u.data)||void 0===v||null===(_=v.error)||void 0===_?void 0:_.message)||"ERROR",Object(l["a"])(p);case 14:t.hideLoader();case 15:case"end":return e.stop()}}),e,null,[[0,10]])})))()},sheduleFile:function(t){this.is_type_user!=this.editor&&this.$router.push("/job/"+t.id+"/upload-files/")},acceptance:function(t){return 100-100*t.images_decline.length/t.images.length},timerFrame:function(t){return parseInt(t.finished_worked_images_sum_timer/t.images.length/60)},searchJob:function(t){var e=t.target.value.toUpperCase();e&&e.length>1?this.jobs_search=this.past_jobs.filter((function(t){return t.style_guide.toUpperCase().includes(e)})):this.jobs_search=this.past_jobs}}),computed:{}},v=u,_=(s("9111"),s("2877")),p=Object(_["a"])(v,a,i,!1,null,"64ee5764",null);e["default"]=p.exports},"909c":function(t,e,s){},9111:function(t,e,s){"use strict";s("909c")}}]);
//# sourceMappingURL=PastJob.ba085202.js.map