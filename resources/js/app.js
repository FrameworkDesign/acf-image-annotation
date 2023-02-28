/*
 |--------------------------------------------------------------------------
 | Load Application Dependencies
 |--------------------------------------------------------------------------
 |
 | First we will load all of this project's JavaScript dependencies which
 | includes Vue and other libraries. It is a great starting point when
 | building robust, powerful web applications using Vue and Laravel.
 |
 */
import "es6-promise/auto";
import Vue from "vue";
import ImageAnnotation from "./components/ImageAnnotation.vue";
// eslint-disable-next-line global-require
Vue.component("image-annotation", ImageAnnotation);

jQuery(document).ready(function () {
  const instance = document.querySelectorAll(".acf-image-annotation-field");

  console.log("instance", instance);

  window.Vue = Vue;
  window.eventBus = new Vue();

  if (instance !== undefined) {
    Array.prototype.forEach.call(instance, (el, i) => {
      const app = new Vue({
        el: el,
      });
    });
  }

  acf.add_action("new_field/type=image_annotation", function (field) {
    // example

    if (field[0].classList.contains("acf-field-image-annotation")) {
      var $fields = acf.findFields({
        type: "flexible_content",
      });

      const fieldFlexibleName = $fields
        .get(0)
        .querySelector(".acf-flexible-content > input[type=hidden]").name;

      const currentInstances = document.querySelectorAll(
        ".acf-image-annotation-vue"
      );

      const layoutsElCount = document.querySelectorAll(
        ".acf-flexible-content .values .layout"
      );

      const fieldHtml = field[0];
      const fieldKey = field[0].getAttribute("data-key");
      //   const layoutKey = fieldHtml.parentElement.parentElement.getAttribute("data-id");
      const layoutKey = $fields.get(0).getAttribute("data-key");
      const postId = jQuery("#post_ID").val();
      const fieldVueHtmlWrapperEl = fieldHtml.querySelector(
        ".acf-image-annotation-field"
      );
      const fieldName = fieldVueHtmlWrapperEl
        .querySelector(".image_innotation_hidden_field_value_name")
        .getAttribute("data-field-name");

      const fieldNameAsClassName = fieldName
        .replaceAll("[", "_")
        .replaceAll("]", "_");

      const rowKey = layoutsElCount.length - 1;

      const newFieldName = `${fieldFlexibleName}[row-${rowKey}][${fieldKey}]`; // acf[${layoutKey}]

      fieldVueHtmlWrapperEl.classList.add(`${fieldNameAsClassName}_clone`);

      const templateWithNewComponent = `<image-annotation post-id="${postId}" field-name="${newFieldName}" field-key="${fieldKey}"></image-annotation>`;
      fieldVueHtmlWrapperEl.innerHTML = templateWithNewComponent;
      const element = document.querySelector(`.${fieldNameAsClassName}_clone`);

      if (_.isNull(element) === false) {
        const app = new Vue({
          el: element,
        });
      }
    }
  });
});
