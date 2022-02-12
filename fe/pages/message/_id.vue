<template>
  <div style="height: 100vh">
    <!-- Image and text -->
    <b-navbar variant="dark" type="dark" style="height: 4.15rem;">
      <b-navbar-brand href="#" v-b-toggle.sidebar-variant class="d-flex align-items-center h-100">
        <b-icon icon="arrow-left" @click="$router.back()"></b-icon>
        <b-img :src="avatar" rounded="circle" class="ml-3 d-inline-block h-100"></b-img>
        <span class="pl-2 font-weight-light">{{ info.name }}</span>
      </b-navbar-brand>
    </b-navbar>

    <b-sidebar id="sidebar-variant" title="" bg-variant="dark" text-variant="light" shadow backdrop right>
      <!-- <template #header="{ hide }">
        <span class="font-weight-light">Me</span>
        <button @click="hide" type="button" class="close text-light" aria-label="Close"><svg viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="x" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-x b-icon bi"><g><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path></g></svg></button>
      </template> -->
      <div class="px-3 pt-2 pb-3 d-flex flex-column justify-content-between h-100">
        <div>
          <b-img :src="avatar" fluid rounded="circle" class="d-block mx-auto w-50"></b-img>

          <h2 class="text-center mt-2">{{ info.name }}</h2>
        </div>
      </div>
    </b-sidebar>

    <b-container class="px-0 overflow-auto" :style="containerHeight" ref="messages">
      <div class="d-flex align-items-end flex-column-reverse pb-1">
        <b-list-group flush class="d-inline-blok w-100 d-flex flex-column-reverse">
          <template v-for="(m, i) in messages">
            <b-list-group-item href="#" class="py-1 pb-1 bg-transparent border-0 d-flex"
              :class="!m.sent_by_me ? 'px-2 align-items-end' : 'pr-2 d-flex-row-reverse align-items-end flex-row-reverse'"
              :key="i">
              <template v-if="!m.sent_by_me">
                <b-img v-b-toggle.sidebar-variant :src="avatar" rounded="circle" class="d-inline-block" style="height: 2rem; margin-bottom: calc((1.5em * .8) + 1px)"></b-img>
              </template>
              <div :class="!m.sent_by_me ? 'ml-2 mr-5 text-left' : 'ml-5 text-right'">
                <b-badge pill
                  :variant="!m.sent_by_me ? 'secondary': 'dark'"
                  class="font-weight-normal font-size-100 text-left form-control w-auto h-auto" style="white-space: pre-line; overflow-wrap: anywhere; line-height: 1.5"
                  >{{ m.text }}
                </b-badge>
                <!-- <small class="d-block" :class="!m.sent_by_me ? 'text-right mr-2' : 'ml-2 text-left'">Seen</small> -->
              </div>
              <!-- <b-img v-show="messages" :src="avatar" rounded="circle" class="px-1 mb-1" style="height: 1rem"></b-img> -->
            </b-list-group-item>
            <small :key="'time-'+i" class="d-block text-center my-1">{{ m.created_datetime}}</small>
          </template>
        </b-list-group>
        <!-- <b-img v-show="messages" :src="avatar" rounded="circle" class="px-1 mb-1" style="height: 1rem"></b-img> -->
      </div>
    </b-container>
    <div style="mx-height: 4.15rem" class="bg-dark px-3 pr-2 py-2">
      <form @submit.prevent="send" class="d-flex flex-row align-items-end">
        <b-form-textarea
          v-model="fields.text"
          placeholder="Aa"
          rows="1"
          no-resize
          :style="'height: calc'+textHeight"
        ></b-form-textarea>
        <!-- <b-button type="submit" variant="secondary" class="send-btn ml-3"> -->
          <!-- <b-icon icon="arrow-right" class="send-btn ml-3"></b-icon> -->
          <b-button  type="submit" variant="secondary" class="ml-2 send-btn" :disabled="invalidText">
            <b-icon icon="arrow-right" variant="light"></b-icon>
          </b-button>
        <!-- </b-button> -->
      </form>
    </div>
  </div>
</template>

<script>
import { BIcon, BIconArrowLeft, BIconArrowRight } from 'bootstrap-vue'
import DefaulAvatar from '@/assets/images/80.jpeg'
export default {
  name: 'MessageView',
  components: { BIcon, BIconArrowLeft, BIconArrowRight },
  data () {
    return {
      avatar: DefaulAvatar,
      info: {
        avatar: DefaulAvatar,
        id: null,
        email: '',
        name: ''
      },
      fields: {
        text: '',
        user_id_to: null
      }
    }
  },
  computed: {
    containerHeight() {
      return 'height: calc(100% - (5.15rem + '+this.textHeight+'))'
    },
    lineCount() {
      return this.fields.text.length ? this.fields.text.split(/\r\n|\r|\n/).length : 0;
    },
    textHeight() {
      if (this.lineCount > 8 ) {
        return this.lineCount > 8 ? '((1.5em * 8) + 1rem + 2px )' : '(1.5em + 1rem + 2px)'
      } else {
        return this.lineCount > 0 ? '((1.5em * '+this.lineCount+') + 1rem + 2px )' : '(1.5em + 1rem + 2px)'
      }
    },
    trimText() {
      // return this.fields.text.replace(/^\s+|\s+$/gm,'')
      return this.fields.text.trim()
    },
    invalidText() {
      return !this.trimText
    },
    contact() {
      return this.$store.getters.contactInfo(this.$route.params.id)
    },
    messages () {
      return this.$store.getters.contactMessages(this.$route.params.id)
    }
  },

  watch: {
    messages(val) {
      // console.log(val)
      setTimeout(() => {
        this.scrollToBottom()
      }, 1);
    },
  },

  async mounted () {
    this.scrollToBottom()
    this.fields.user_id_to = this.$route.params.id
    // await this.$store.dispatch('fetchContact', this.$route.params.id)
    if (!this.contact) {
      await this.fetchContact(this.$route.params.id)
    } else {
      this.info = this.contact
    }
    // await this.$store.dispatch('fetchContactMessages', this.$route.params.id)
    this.scrollToBottom()
  },

  methods: {
    async fetchContact (id) {
      await this.$axios.get('/users/'+id)
        .then((r) => {
          this.info = r.data.data
        })
    },

    send () {
      this.$store.dispatch('insertContactMessage', {
        contact: this.info,
        message: {
          ...this.fields,
          created_at: this.$moment().format("YYYY-MM-DD HH:mm:ss"),
          created_datetime: this.$moment().format("YYYY-MM-DD HH:mm:ss"),
          deleted_at: null,
          id: null,
          sent_by_me: 1,
          updated_at: this.$moment().format("YYYY-MM-DD HH:mm:ss"),
          user_id_from: this.$auth.user.id
        }
       })
      this.scrollToBottom()
      this.$nextTick(() => this.fields.text = '')
      this.$axios.post('/send-message', this.fields)
        // .then((r) => {
        //   console.log(r)
        // })
    },

    scrollToBottom () {
      let container = this.$refs.messages;
      container.scrollTop = container.scrollHeight;
    },

    logout () {
      this.$auth.logout()
    }
  }
}
</script>
