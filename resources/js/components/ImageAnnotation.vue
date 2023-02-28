<template>
  <section class="acf-image-annotation acf-image-annotation-vue">
    <div class="acf-input">
      <div class="acf-fields -top -border">
        <div
          class="acf-field acf-field-text acf-field-5aeace0a361ef"
          data-name="Image"
          data-type="text"
          data-key=""
        >
          <div class="acf-label">
            <label>Image</label>
            <span
              class="acf-icon -cancel dark"
              v-if="preview !== null"
              @click="reset"
            ></span>
          </div>
          <div class="acf-input">
            <div class="acf-input-wrap">
              <div class="image-preview-wrapper">
                <div id="image-preview" class="c-preview">
                  <img
                    :src="preview"
                    id="preview-img"
                    ref="imageItem"
                    @click="getClickPosition"
                  />
                  <div
                    class="c-annotate"
                    v-for="(annotate, index) in annotations"
                    :style="styleItem(annotate)"
                    :key="index"
                    v-text="`${index + 1}`"
                  ></div>
                </div>
              </div>
              <a
                class="acf-button button"
                v-if="showUploadButton"
                @click.prevent="openMediaFrame"
                >Add Image</a
              >
            </div>
          </div>
        </div>
      </div>
    </div>
    <br>
    <br>
    <div class="acf-fields">
      <label>Options</label>
      <div class="acf-field">
        <label for="image_innotation_hidden_field_hasRegions">
          <input v-model="hasRegions" class="hide-postbox-tog" name="image_innotation_hidden_field_hasRegions" type="checkbox" id="image_innotation_hidden_field_hasRegions">
          Has regions? (regions on hover)
        </label>
      </div>
      <div class="acf-field">
        <label for="image_innotation_hidden_field_hasModalPopups">
          <input v-model="hasModalPopups" class="hide-postbox-tog" name="image_innotation_hidden_field_hasModalPopups" type="checkbox" id="image_innotation_hidden_field_hasModalPopups">
          Has Modal Popups? (clicking each item is a popup/modal)
        </label>
      </div>
    </div>
    <!-- Lists -->
    <div class="acf-field acf-field-repeater">
      <br />
      <div class="acf-label">
        <label
          >Annotations</label
        >
      </div>
      <div class="acf-input">
        <div class="acf-repeater -table" data-min="0" data-max="0">
          <table class="acf-table">
            <thead>
              <tr>
                <th class="acf-row-handle"></th>
                <th
                  class="acf-th"
                  data-name="item"
                  data-type="text"
                  style="width: 100%"
                >
                  Item
                </th>
                <th class="acf-row-handle"></th>
              </tr>
            </thead>
            <tbody v-if="annotations">
              <tr
                class="acf-row"
                v-for="(item, index) in annotations"
                :key="index"
                data-id=""
              >
                <td class="acf-row-handle order" title="Drag to reorder">
                  <span v-text="`${index + 1}`"></span>
                </td>
                <!-- -->
                <td class="acf-field acf-field-text">
                  <div class="acf-input">
                    <div class="acf-label">
                      <label>Title</label>
                    </div>
                    <div class="acf-input-wrap">
                      <input
                        type="text"
                        name="itemTitle"
                        v-model="item.title"
                      />
                    </div>
                  </div>
                  <br />
                  <div class="acf-input">
                    <div class="acf-label">
                      <label>Summary</label>
                    </div>
                    <div class="acf-input-wrap">
                      <textarea
                        type="text"
                        name="itemSummary"
                        v-model="item.summary"
                      ></textarea>
                    </div>
                  </div>
                  <ImageAnnotationRow
                    v-for="(row, rIndex) in item.rows"
                    :key="rIndex"
                    :post-id="postId"
                    :index="index"
                    :row-index="rIndex"
                    :row.sync="row"
                    @delete="removeItemRow"
                  ></ImageAnnotationRow>

                  <a
                    class="acf-button button button-primary"
                    href="#"
                    @click.prevent="addRowToItem(item, index)"
                  >
                    Add row
                  </a>
                </td>
                <!-- -->
                <td class="acf-row-handle remove">
                  <span
                    class="acf-icon -minus small"
                    @click="removeItem(index)"
                  ></span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <input
      type="hidden"
      :name="this.fieldName"
      class="image_innotation_hidden_field_value"
      :value="dataToSave"
    />
    <input
      type="hidden"
      class="image_innotation_hidden_field_value_name"
      :data-field-name="uniqueFieldName"
    />
  </section>
</template>
<script>
import ImageAnnotationRow from "./ImageAnnotationRow";
export default {
  components: {
    ImageAnnotationRow,
  },
  props: {
    postId: {
      type: [Number, String],
      default: 0,
    },
    fieldName: {
      type: String,
      default: "",
    },
    fieldKey: {
      type: String,
      default: "",
    },
    fieldData: {
      type: [Array, Object, String],
      default() {
        return null;
      },
    },
  },
  mounted() {
    if (this.fieldData !== null) {
      const fieldData = JSON.parse(this.fieldData);
      this.preview = fieldData.image;
      this.height = fieldData.height;
      this.width = fieldData.width;
      this.annotations = fieldData.annotations;
      this.id = fieldData.imageId;
    }
  },
  data() {
    return {
      hasRegions: false,
      hasModalPopups: false,
      mediaFrame: null,
      preview: null,
      height: 0,
      width: 0,
      id: null,
      annotations: [],
    };
  },
  computed: {
    showUploadButton() {
      if (this.fieldData == null) {
        return true;
      }
      return this.fieldData.imageId == null ? true : false;
    },
    uniqueFieldName() {
      return `${this.fieldName}`;
    },
    styles: function () {
      return {
        backgroundImage: `url(${this.preview})`,
        width: `${this.width}px`,
        height: `${this.height}px`,
      };
    },
    dataToSave: function () {
      const dataObj = {
        hasRegions: this.hasRegions,
        hasModalPopups: this.hasModalPopups,
        imageId: this.id,
        image: this.preview,
        height: this.height,
        width: this.width,
        annotations: this.annotations,
      };

      return JSON.stringify(dataObj);
    },
  },
  methods: {
    openMediaFrame() {
      if (this.mediaFrame) {
        this.mediaFrame.uploader.uploader.param(
          "post_id",
          parseInt(this.postId)
        );
        // open frame
        this.mediaFrame.open();
        return;
      } else {
        // set the wp.media post id so the uploader grabs the ID we want when initialised
        wp.media.model.settings.post.id = parseInt(this.postId);
      }

      // create the media frame.
      this.mediaFrame = wp.media.frames.file_frame = wp.media({
        title: "Select a image to upload",
        button: {
          text: "Use this image",
        },
        multiple: false,
      });

      // when an image is selected, run a callback
      this.mediaFrame.on("select", () => {
        let attachment = this.reactiveJson(
          this.mediaFrame.state().get("selection").first()
        );

        this.id = attachment.id;
        this.preview = attachment.url;
        this.width = attachment.width;
        this.height = attachment.height;
      });

      // open the model
      this.mediaFrame.open();
    },
    getClickPosition(e) {
      const parentPosition = this.getPosition(e.currentTarget);
      const xPosition = e.clientX - parentPosition.x - 30 / 2;
      const yPosition = e.clientY - parentPosition.y - 30 / 2;
      const width = this.$refs.imageItem.clientWidth;
      const height = this.$refs.imageItem.clientHeight;

      // convert position to percentage values
      let x = this.roundUp((xPosition / width) * 100, 0);
      let y = this.roundUp((yPosition / height) * 100, 0);

      // push new annotations
      this.annotations.push({
        x: x,
        y: y,
        title: null,
        summary: null,
        rows: [],
      });
    },
    getPosition(el) {
      let xPos = 0;
      let yPos = 0;

      while (el) {
        if (el.tagName == "BODY") {
          // deal with browser quirks with body/window/document and page scroll
          const xScroll = el.scrollLeft || document.documentElement.scrollLeft;
          const yScroll = el.scrollTop || document.documentElement.scrollTop;

          xPos += el.offsetLeft - xScroll + el.clientLeft;
          yPos += el.offsetTop - yScroll + el.clientTop;
        } else {
          // for all other non-BODY elements
          xPos += el.offsetLeft - el.scrollLeft + el.clientLeft;
          yPos += el.offsetTop - el.scrollTop + el.clientTop;
        }

        el = el.offsetParent;
      }

      return {
        x: xPos,
        y: yPos,
      };
    },
    reactiveJson(dataObj) {
      return JSON.parse(JSON.stringify(dataObj));
    },
    roundUp(num, precision) {
      precision = Math.pow(10, precision);
      return Math.ceil(num * precision) / precision;
    },
    styleItem(coordinate) {
      return {
        top: `${coordinate.y}%`,
        left: `${coordinate.x}%`,
      };
    },
    addRowToItem(item, index) {
      item = this.reactiveJson(item);
      if (item.rows === undefined) {
        item.rows = [];
      }
      item.rows.push({
        backgroundImageId: null,
        backgroundImage: null,
        logoImageId: null,
        logoImage: null,
        title: null,
        subTitle: null,
        copy: null,
        link: null,
      });

      this.$set(this.annotations, index, item);
    },
    removeItemRow({ rowIndex, index }) {
      let annotations = this.reactiveJson(this.annotations);
      let indexRow = annotations[index];
      indexRow = indexRow.rows.splice(rowIndex, 1);
      annotations[index].rows = indexRow;

      this.annotations = annotations;
    },
    removeItem(index) {
      this.annotations.splice(index, 1);
    },
    itemName(index) {
      return `${this.fieldName}[${index}]`;
    },
    reset() {
      this.width = 0;
      this.height = 0;
      this.preview = null;
      this.annotations = [];
    },
  },
};
</script>

<style>
.relative {
  position: relative;
}
.c-preview {
  /* Box-model */
  max-width: 100%;

  /* Positioning */
  position: relative;

  /* Visual */
  /* background-size: contain;
    background-repeat: no-repeat; */

  /* Misc */
  cursor: crosshair;

  background-color: gainsboro;
}

.c-preview img {
  width: 100%;
  height: auto;
}

.c-preview-row,
.c-preview-row img {
  max-width: 200px;
}

.c-annotate {
  /* Positioning */
  position: absolute;

  /* Visual */
  background-color: #182753;
  color: #ffffff;

  /* Box-model */
  height: 30px;
  width: 30px;
  border-radius: 50%;
  text-align: center;
  line-height: 30px;
  font-weight: 700;
}

.image-preview-wrapper {
  position: relative;
}

span.acf-icon.dark {
  position: absolute;
  top: 0;
  right: 0;
}

.image-preview-wrapper:hover span.acf-icon.dark {
  display: block;
}

span.acf-icon {
  /* Misc */
  cursor: pointer;
  color: #999;
  border-color: #bbb;
  background-color: #fff;
}

span.acf-icon:hover {
  /* Visual */
  color: #fff;
  border-color: transparent;
  background-color: #f55e4f;
}
</style>
