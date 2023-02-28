<template>
  <div class="annotation-row">
    <div class="annotation-row__top">
      <p><strong v-text="`Row ${rowIndex + 1}`"></strong></p>
      <div>
        <div class="relative">
          <span
            class="acf-icon acf-icon--small -cancel dark"
            @click="remove"
          ></span>
        </div>
        <a
          class="acf-button button"
          style="margin-right: 40px"
          @click="showRow = !showRow"
          >Show/Hide</a
        >
      </div>
    </div>

    <div v-show="showRow">
      <div class="acf-input">
        <div class="acf-label">
          <label>Background Image</label>
          <span
            class="acf-icon acf-icon--small -cancel dark"
            v-if="row.backgroundImage !== null"
            @click="resetBackgroundImage"
          ></span>
        </div>
        <div class="acf-input-wrap">
          <div class="image-preview-wrapper">
            <div class="c-preview c-preview-row">
              <img :src="row.backgroundImage" />
            </div>
          </div>
          <a
            class="acf-button button"
            v-if="showBgUploadButton"
            @click.prevent="openBackgroundImageMediaFrame"
            >Add Background Image</a
          >
        </div>
      </div>

      <div class="acf-input">
        <div class="acf-label">
          <label>Logo</label>
          <span
            class="acf-icon acf-icon--small -cancel dark"
            v-if="row.logoImage !== null"
            @click="resetLogoImage"
          ></span>
        </div>
        <div class="acf-input-wrap">
          <div class="image-preview-wrapper">
            <div class="c-preview c-preview-row">
              <img :src="row.logoImage" />
            </div>
          </div>
          <a
            class="acf-button button"
            v-if="showUploadButton"
            @click.prevent="openLogoMediaFrame"
            >Add Logo</a
          >
        </div>
      </div>
      <br />
      <div class="acf-input">
        <div class="acf-label">
          <label>Title</label>
        </div>
        <div class="acf-input-wrap">
          <input type="text" name="itemName" v-model="row.title" />
        </div>
      </div>
      <br />
      <div class="acf-label">
        <label>Sub-Title</label>
      </div>
      <div class="acf-input-wrap">
        <input type="text" name="itemName" v-model="row.subTitle" />
      </div>
      <br />
      <div class="acf-input">
        <div class="acf-label">
          <label>Copy</label>
        </div>
        <div class="acf-input-wrap">
          <textarea
            type="text"
            name="itemSummary"
            v-model="row.copy"
          ></textarea>
        </div>
      </div>

      <div class="acf-input">
        <div class="acf-label">
          <label>Link</label>
        </div>
        <div class="acf-input-wrap acf-url">
          <i class="acf-icon -globe -small"></i>
          <input type="url" name="itemName" v-model="row.link" />
        </div>
      </div>
      <br />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    postId: {
      type: [Number, String],
      default: 0,
    },
    index: {
      type: [Number, String],
      default: 0,
    },
    rowIndex: {
      type: [Number, String],
      default: 0,
    },
    row: {
      type: [Array, Object, String],
      default() {
        return null;
      },
    },
  },
  mounted() {
    if (this.row.backgroundImage === undefined) {
      this.row.backgroundImage = null;
      this.row.backgroundImageId = null;
    }

    console.log("this.row.", this.row);
  },
  data() {
    return {
      showRow: false,
      mediaFrameLogo: null,
      mediaFrameBg: null,
      height: 0,
      width: 0,
    };
  },

  computed: {
    showUploadButton() {
      return this.row.logoImage === null ? true : false;
    },
    showBgUploadButton() {
      return this.row.backgroundImage === null ? true : false;
    },
  },

  methods: {
    remove() {
      if (confirm("Are you sure?")) {
        this.$emit("delete", {
          index: this.index,
          rowIndex: this.rowIndex,
        });
      }
    },
    openLogoMediaFrame() {
      this.openMediaFrameLogo();

      // when an image is selected, run a callback
      this.mediaFrameLogo.on("select", () => {

        let attachments = this.reactiveJson(this.mediaFrameLogo.state().get("selection"))
        console.log('openLogoMediaFrame', attachments)

        let attachment = this.reactiveJson(
          this.mediaFrameLogo.state().get("selection").first()
        );
        this.row.logoImageId = attachment.id;
        this.row.logoImage = attachment.url;
        // this.width = attachment.width;
        // this.height = attachment.height;
      });

      // open the model
      this.mediaFrameLogo.open();
    },
    openBackgroundImageMediaFrame() {
      this.openMediaFrameBgImage();

      // when an image is selected, run a callback
      this.mediaFrameBg.on("select", () => {

        let attachments = this.reactiveJson(this.mediaFrameBg.state().get("selection"))
        console.log('openBackgroundImageMediaFrame', attachments)

        let attachment = this.reactiveJson(
          this.mediaFrameBg.state().get("selection").first()
        );
        this.row.backgroundImageId = attachment.id;
        this.row.backgroundImage = attachment.url;
      });

      // open the model
      this.mediaFrameBg.open();
    },
    openMediaFrameLogo() {
      if (this.mediaFrameLogo) {
        this.mediaFrameLogo.uploader.uploader.param(
          "post_id",
          parseInt(this.postId)
        );
        // open frame
        this.mediaFrameLogo.open();
        return;
      } else {
        // set the wp.media post id so the uploader grabs the ID we want when initialised
        wp.media.model.settings.post.id = parseInt(this.postId);
      }

      // create the media frame.
      this.mediaFrameLogo = wp.media.frames.file_frame = wp.media({
        title: "Select a image to upload",
        button: {
          text: "Use this image",
        },
        multiple: false,
      });
    },
    openMediaFrameBgImage() {
      if (this.mediaFrameBg) {
        this.mediaFrameBg.uploader.uploader.param(
            "post_id",
            parseInt(this.postId)
        );
        // open frame
        this.mediaFrameBg.open();
        return;
      } else {
        // set the wp.media post id so the uploader grabs the ID we want when initialised
        wp.media.model.settings.post.id = parseInt(this.postId);
      }

      // create the media frame.
      this.mediaFrameBg = wp.media.frames.file_frame = wp.media({
        title: "Select a image to upload",
        button: {
          text: "Use this image",
        },
        multiple: false,
      });
    },
    reactiveJson(dataObj) {
      return JSON.parse(JSON.stringify(dataObj));
    },
    resetLogoImage() {
      this.width = 0;
      this.height = 0;
      this.row.logoImage = null;
      this.row.logoImageId = null;
    },
    resetBackgroundImage() {
      this.row.backgroundImage = null;
      this.row.backgroundImageId = null;
    },
  },
};
</script>

<style scoped>
.annotation-row {
  margin: 10px 0;
  padding: 10px;
  border: 1px solid #dfdfdf;
}

.annotation-row__top {
  position: relative;
  display: flex;
  justify-content: space-between;
  border-bottom: 1px solid #dfdfdf;
  margin-bottom: 6px;
  padding-bottom: 6px;
}

.acf-icon--small {
  height: 20px;
  width: 20px;
  border: transparent solid 1px;
  border-radius: 100%;
  font-size: 17px;
  line-height: 1;
}
</style>
