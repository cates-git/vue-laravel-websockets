<template>
  <div>
    <!-- Image and text -->
    <b-navbar variant="dark" type="dark" fixed>
      <b-navbar-brand href="#" v-b-toggle.sidebar-variant class="d-flex align-items-center h-100">
        <!-- <b-img :src="avatar" rounded="circle" class="d-inline-block h-100"></b-img> -->
        <Avatar :name="$auth.user.name"/>
        <span class="pl-2 font-weight-bold">Chats</span>
      </b-navbar-brand>
    </b-navbar>

    <b-sidebar id="sidebar-variant" title="Me" bg-variant="dark" text-variant="light" shadow backdrop>
      <div class="px-3 pt-2 pb-3 d-flex flex-column justify-content-between h-100">
        <div class="text-center">
          <!-- <b-img :src="avatar" fluid rounded="circle" class="d-block mx-auto w-50"></b-img> -->

          <h2 class="mt-2">{{ $auth.user.name }}</h2>
          <span>{{ $auth.user.email }}</span>
        </div>

        <b-button @click="logout" block class="text-uppercase">Sign Out</b-button>
      </div>
    </b-sidebar>

    <b-container class="px-0">
      <div class="px-4 pt-2 pb-1">
        <SearchModal ref="SearchModal" @submit="message"/>
      </div>

      <b-list-group flush>
        <b-list-group-item
          v-for="(contact, i) in $store.getters.messages" :key="i"
          @click="$refs.MessageModal.show(contact.id)"
          href="#" class="bg-transparent pt-2 pb-2 border-0 contact-list">

          <Avatar :name="contact.name" height="3.15rem" width="3.15rem" class="d-inline-block align-middle"/>
          <!-- <b-img :src="avatar" rounded="circle" class="d-inline-block align-middle"></b-img> -->

          <div class="d-inline-block align-middle pl-3 text-truncate" style="width: calc(100% - 4.3rem)">
            <h6 class="mb-0 w-100 d-flex justify-content-between align-items-end">
              <span>{{ contact.name }}</span>
              <small>{{ contact.email }}</small>
            </h6>
            <small>{{ contact.messages[0].text }}</small>
          </div>
        </b-list-group-item>
      </b-list-group>
    </b-container>
    <MessageModal ref="MessageModal"/>
  </div>
</template>

<script>
import DefaulAvatar from '@/assets/images/80.jpeg'
import SearchModal from './SearchModal.vue'
import MessageModal from './MessageModal.vue'
import Avatar from '~/components/Avatar.vue'
export default {
  name: 'IndexPage',
  components: { Avatar, SearchModal, MessageModal },
  data () {
    return {
      avatar: DefaulAvatar,
      fields: {
        text: '',
        user_id_to: 4
      },
      list: []
    }
  },
  async created () {
    await this.$store.dispatch('fetchMessages')
  },
  mounted () {
    this.$echo.channel('message.' + this.$auth.user.id)
      .listen('Message', (e) => {
        this.$store.dispatch('insertContactMessage', e.new)
      })
  },
  methods: {
    logout () {
      this.$auth.logout()
    },
    message (id) {
      this.$refs.MessageModal.show(id)
    }
  }
}
</script>
