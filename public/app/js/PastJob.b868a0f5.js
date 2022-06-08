(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["PastJob"],{"0640":function(e,t,a){"use strict";a("6839")},6839:function(e,t,a){},"7acb":function(e,t,a){"use strict";a.r(t);var i=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("main-layout",{attrs:{"is-show-header":!1}},[i("div",{staticClass:"box_page"},[i("div",{staticClass:"title_header"},[e._v("Past Jobs")]),i("div",{staticClass:"pt-4"},[i("div",{staticClass:"input_box"},[i("input",{directives:[{name:"model",rawName:"v-model",value:e.search_key,expression:"search_key"}],staticClass:"icon_input_search style_input1",attrs:{type:"text"},domProps:{value:e.search_key},on:{keyup:e.searchJob,input:function(t){t.target.composing||(e.search_key=t.target.value)}}}),i("img",{staticClass:"filter_icon",attrs:{src:a("2376"),alt:"icon-approved.svg"}}),i("img",{staticClass:"search_icon",attrs:{src:a("d103"),alt:""}})])]),i("div",{staticClass:"jobs_box"},[e.isEmptyObject(e.jobs_search)?e._l(e.jobs_search,(function(t){return i("div",{staticClass:"item_job"},[i("div",{staticClass:"item_j"},[i("div",{staticClass:"box_i"},[i("div",{staticClass:"box_image",on:{click:function(a){return e.sheduleFile(t)}}},[i("img",{directives:[{name:"lazy",rawName:"v-lazy",value:e.getJobImages(t)[0],expression:"getJobImages(job)[0]"}],staticClass:"job_image",attrs:{alt:"icon-approved.svg"}})]),i("div",{staticClass:"ms-2 box_i2"},[i("div",{staticClass:"description_item_j"},[t.style_guide?i("div",[i("span",{staticClass:"text_bold"},[e._v("Style Guide:")]),e._v(" "+e._s(t.style_guide)+" ")]):e._e(),i("div",[i("span",{staticClass:"text_bold"},[e._v("Files:")]),e._v(" "+e._s(e.getJobImages(t).length)+" ")]),i("div",[i("span",{staticClass:"text_bold"},[e._v("Editing Level:")]),e._v(" "+e._s(t.editing_level)+" ")]),i("div",[i("span",{staticClass:"text_bold"},[e._v("Acceptance:")]),e._v(" "+e._s(e.acceptance(t))+"% ")]),i("div",[i("span",{staticClass:"text_bold"},[e._v("Time Frame:")]),e._v(" "+e._s(e.timerFrame(t))+" min/avg ")]),e.is_type_user===e.business?[e.isEmptyObject(t.review)?i("div",{staticClass:"rating_job"},[i("star-rating",{attrs:{"show-rating":!1,"star-size":14,"inactive-color":"#D1D2D3","active-color":"#D8C3AF","read-only":!0},model:{value:t.review.rating,callback:function(a){e.$set(t.review,"rating",a)},expression:"job.review.rating"}})],1):i("div",{staticClass:"leave-review",on:{click:function(a){return e.openReviewModal(t)}}},[e._v(" Leave Review ")])]:e._e()],2)])])])])})):i("div",{staticClass:"empty_jobs"},[e._v("Here will be your jobs.")])],2)]),i("div",{staticClass:"modal",attrs:{id:"reviewModal",tabindex:"-1"}},[i("div",{staticClass:"modal-dialog modal-dialog-centered"},[i("div",{staticClass:"modal-content"},[i("div",{staticClass:"modal-header"},[i("h5",{staticClass:"modal-title review_title"},[e._v("Tell everyone how was it to work with "+e._s(e.add_review.editor_name))])]),i("div",{staticClass:"modal-body"},[i("div",{staticClass:"box_body"},[i("star-rating",{attrs:{"show-rating":!1,"star-size":22,"inactive-color":"#D1D2D3","active-color":"#D8C3AF"},model:{value:e.add_review.rating,callback:function(t){e.$set(e.add_review,"rating",t)},expression:"add_review.rating"}}),i("textarea",{directives:[{name:"model",rawName:"v-model",value:e.add_review.message,expression:"add_review.message"}],attrs:{rows:"4",placeholder:"Leave a review..."},domProps:{value:e.add_review.message},on:{input:function(t){t.target.composing||e.$set(e.add_review,"message",t.target.value)}}})],1)]),i("div",{staticClass:"modal-footer"},[i("button",{staticClass:"btn_modal_t1",attrs:{"data-bs-dismiss":"modal"}},[e._v("Cancel")]),i("button",{staticClass:"btn_modal_t2",on:{click:e.postReview}},[e._v("Post")])])])])])])},s=[],r=a("1da1"),o=a("5530"),n=(a("96cf"),a("b64b"),a("d81d"),a("07ac"),a("4de4"),a("d3b7"),a("caad"),a("2532"),a("713b")),d=a("09f9"),l=a("2f62"),c=a("3703"),v=a("7b17"),u=a("5b3d"),_=a.n(u),m={name:"PastJob",components:{MainLayout:n["a"],StarRating:_.a},data:function(){return{past_jobs:{},search_key:null,jobs_search:{},is_type_user:null,business:c["a"].BUSINESS,editor:c["a"].EDITOR,reviewModal:null,add_review:{editor_name:"",rating:0,editor_id:null,image_job_id:null,message:""}}},destroyed:function(){this.reviewModal&&this.reviewModal.dispose()},mounted:function(){this.pastJobs(),this.reviewModal=new v["a"](document.getElementById("reviewModal"))},methods:Object(o["a"])(Object(o["a"])({},Object(l["c"])(["showLoader","hideLoader"])),{},{isEmptyObject:function(e){return e?0!==Object.keys(e).length:0},postReview:function(){var e=this;return Object(r["a"])(regeneratorRuntime.mark((function t(){var a,i,s,r;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(e.add_review.rating){t.next=3;break}return Object(d["a"])("Rating is required"),t.abrupt("return");case 3:return t.prev=3,e.showLoader(),t.next=7,e.$http.postAuth("".concat(e.$http.apiUrl(),"create-review"),{to_user_id:e.add_review.editor_id,job_image_id:e.add_review.image_job_id,message:e.add_review.message,rating:e.add_review.rating});case 7:t.next=13;break;case 9:t.prev=9,t.t0=t["catch"](3),r=(null===t.t0||void 0===t.t0||null===(a=t.t0.response)||void 0===a||null===(i=a.data)||void 0===i||null===(s=i.error)||void 0===s?void 0:s.message)||"ERROR",Object(d["a"])(r);case 13:return t.next=15,e.pastJobs();case 15:e.hideLoader(),e.clearAddReview(),e.reviewModal.hide();case 18:case"end":return t.stop()}}),t,null,[[3,9]])})))()},openReviewModal:function(e){var t,a,i;this.add_review.editor_id=(null===e||void 0===e||null===(t=e.user_work)||void 0===t?void 0:t.user_id)||null,this.add_review.image_job_id=e.id,this.add_review.editor_name=(null===e||void 0===e||null===(a=e.user_work)||void 0===a||null===(i=a.user)||void 0===i?void 0:i.first_name)||"",this.reviewModal.show()},clearAddReview:function(){this.add_review.rating=0,this.add_review.editor_id=null,this.add_review.image_job_id=null,this.add_review.message=""},getJobImages:function(e){return null!==e&&void 0!==e&&e.images?Object.values(null===e||void 0===e?void 0:e.images).map((function(e){var t;return null!==(t=e.src_cropped)&&void 0!==t?t:e.src})):[]},pastJobs:function(){var e=this;return Object(r["a"])(regeneratorRuntime.mark((function t(){var a,i,s,r,o,n,l,c,v,u,_,m;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.prev=0,e.showLoader(),t.next=4,e.$http.getAuth("".concat(e.$http.apiUrl(),"past-job"));case 4:c=t.sent,e.past_jobs=(null===c||void 0===c||null===(a=c.data)||void 0===a||null===(i=a.data)||void 0===i?void 0:i.past_jobs)||{},e.jobs_search=(null===c||void 0===c||null===(s=c.data)||void 0===s||null===(r=s.data)||void 0===r?void 0:r.past_jobs)||{},e.is_type_user=(null===c||void 0===c||null===(o=c.data)||void 0===o||null===(n=o.data)||void 0===n||null===(l=n.user)||void 0===l?void 0:l.type_user)||null,t.next=14;break;case 10:t.prev=10,t.t0=t["catch"](0),m=(null===t.t0||void 0===t.t0||null===(v=t.t0.response)||void 0===v||null===(u=v.data)||void 0===u||null===(_=u.error)||void 0===_?void 0:_.message)||"ERROR",Object(d["a"])(m);case 14:e.hideLoader();case 15:case"end":return t.stop()}}),t,null,[[0,10]])})))()},sheduleFile:function(e){this.is_type_user!=this.editor&&this.$router.push("/job/"+e.id+"/upload-files/")},acceptance:function(e){return 100-100*e.images_decline.length/e.images.length},timerFrame:function(e){return parseInt(e.finished_worked_images_sum_sum_timers/e.images.length/60)},searchJob:function(e){var t=e.target.value.toUpperCase();t&&t.length>1?this.jobs_search=this.past_jobs.filter((function(e){return e.style_guide.toUpperCase().includes(t)})):this.jobs_search=this.past_jobs}}),computed:{}},p=m,b=(a("0640"),a("2877")),g=Object(b["a"])(p,i,s,!1,null,"bc80caea",null);t["default"]=g.exports}}]);
//# sourceMappingURL=PastJob.b868a0f5.js.map