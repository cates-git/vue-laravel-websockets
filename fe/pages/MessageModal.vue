<template>
  <div class="message-modal">
    <b-modal v-model="modalShow" hide-footer hide-header  modal-class="message-modal">
      <div class="h-100">
        <b-navbar variant="dark" type="dark" ref="message-header" class="message-header">
          <b-navbar-brand href="#" v-b-toggle.sidebar-variant1 class="d-flex align-items-center h-100">
            <b-icon icon="arrow-left" @click="close()"></b-icon>
            <Avatar :name="info.name" class="ml-3"/>
            <span class="pl-2 font-weight-light">{{ info.name }}
              <small class="d-block" style="margin-top: -3px;">{{ $auth.user.email }}</small>
            </span>

          </b-navbar-brand>
        </b-navbar>

        <!-- <b-sidebar id="sidebar-variant1" title="" bg-variant="dark" text-variant="light" shadow backdrop right>
          <div class="px-3 pt-2 pb-3 d-flex flex-column justify-content-between h-100">
            <div class="text-center">
              <b-img :src="avatar" fluid rounded="circle" class="d-block mx-auto w-50"></b-img>

              <h2 class="mt-2">{{ info.name }}</h2>

              <span>{{ $auth.user.email }}</span>
            </div>
          </div>
        </b-sidebar> -->

        <b-container class="px-0 overflow-auto" :style="containerHeight" ref="messages">
          <div class="d-flex align-items-end flex-column-reverse pb-1">
            <b-list-group flush class="d-inline-blok w-100 d-flex flex-column-reverse">
              <template v-for="(m, i) in messages">
                <b-list-group-item href="#" class="py-1 pb-1 bg-transparent border-0 d-flex"
                  :class="!m.sent_by_me ? 'px-2 align-items-end' : 'pr-2 d-flex-row-reverse align-items-end flex-row-reverse'"
                  :key="i">
                  <template v-if="!m.sent_by_me">
                    <Avatar :name="info.name" class="ml-3" height="1.85rem" width="1.85rem"/>
                    <!-- <b-img v-b-toggle.sidebar-variant :src="avatar" rounded="circle" class="d-inline-block" style="height: 1.85rem;"></b-img> -->
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
        <div style="bottom: -1px" class="bg-dark fixed-bottom">
          <form @submit.prevent="send" class="d-flex flex-row align-items-end pl-3 pr-2 py-2">
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
    </b-modal>
  </div>
</template>

<script>
  import Avatar from '~/components/Avatar.vue'
  import { BIcon, BIconArrowLeft, BIconArrowRight } from 'bootstrap-vue'
  import DefaulAvatar from '@/assets/images/80.jpeg'
  export default {
    name: 'MessageModal',
    components: { Avatar, BIcon, BIconArrowLeft, BIconArrowRight },
    data() {
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
        },
        modalShow: false,
        search: '',
        results: []
      }
    },
    computed: {
      containerHeight() {
        // if (this.$refs['message-header']) {
        //   let h = this.$refs['message-header'].$el.scrollHeight;
        //   return 'height: calc(100% - ('+h+'px + '+this.textHeight+'))';
        // } else {
          return 'height: calc(100% - (4.75rem + '+this.textHeight+'))';
        // }
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
        return this.fields.text.trim()
      },
      invalidText() {
        return !this.trimText
      },
      contact() {
        return this.$store.getters.contactInfo(this.info.id)
      },
      messages () {
        return this.$store.getters.contactMessages(this.info.id)
      }
    },

    watch: {
      messages() {
        setTimeout(() => {
          this.scrollToBottom()
        }, 1);
      },
    },

    mounted() {
      document.addEventListener('backbutton', function(){
        this.close()
        return false;
      });
    },
    methods: {
      async show (id) {
        this.modalShow = true

        this.fields.user_id_to = id
        if (!this.contact) {
          await this.fetchContact(id)
        } else {
          this.info = this.contact
        }
        this.scrollToBottom()
      },
      close () {
        this.modalShow = false
        this.info.id = null
      },
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
      },

      scrollToBottom () {
        if (this.$refs.messages) {
          let container = this.$refs.messages;
          container.scrollTop = container.scrollHeight;
        }
      },
    }
  }
</script>
