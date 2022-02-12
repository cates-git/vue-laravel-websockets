<template>
  <div>
    <b-button @click="show" type="button" block class="text-left btn-search" variant="outline-dark">Search</b-button>

    <b-modal v-model="modalShow" hide-footer hide-header>
      <header class="header">
        <div style="width: 1.5rem" class="d-inline-block align-middle h-100">
          <b-icon icon="arrow-left" @click="close" class="h-100 fs-1" style="font-size: 1rem"></b-icon>
        </div>
        <b-form-input v-model="search" ref="searchInput" type="text" placeholder="Search" class="form-control d-inline-block align-middle" style="width: calc(100% - 3rem - 8px); border: 0" autofocus>
        </b-form-input>
        <div v-if="search" style="width: 1.5rem:" class="d-inline-block align-middle h-100 text-right">
          <b-icon icon="x" @click="clear" class="h-100 fs-1-25"></b-icon>
        </div>
      </header>

      <b-container class="px-0 overflow-auto" style="height: calc(100% - 4.15rem)">
        <b-list-group flush>
          <b-list-group-item
            v-for="(contact, i) in results" :key="i"
            @click="select(contact.id)"
            href="#" class="bg-transparent py-2 border-0">
            <!-- <b-img :src="avatar" rounded="circle" class="d-inline-block align-middle" style="height: 2.75rem; width: 2.75rem"></b-img> -->

            <Avatar :name="contact.name" height="2.75rem" width="2.75rem" class="d-inline-block align-middle"/>
            <div class="d-inline-block align-middle pl-2 text-truncate" style="width: calc(100% - 3rem)">
              <h6 class="mb-0 w-100" style="line-height: 1">{{ contact.name }}</h6>
              <small>{{ contact.email }}</small>
            </div>
          </b-list-group-item>
        </b-list-group>
      </b-container>
    </b-modal>
  </div>
</template>

<script>
  import Avatar from '~/components/Avatar.vue'
  import { BIcon, BIconArrowLeft, BIconX } from 'bootstrap-vue'
  import DefaulAvatar from '@/assets/images/80.jpeg'
  export default {
    name: 'SearchModal',
    components: { Avatar, BIcon, BIconArrowLeft, BIconX },
    data() {
      return {
        avatar: DefaulAvatar,
        modalShow: false,
        search: '',
        results: []
      }
    },
    watch: {
      search(val) {
        this.$axios.get('/users', { params: { search: val } })
          .then((r) => {
            this.results = r.data.data
          })
      }
    },
    methods: {
      show () {
        this.modalShow = true
      },
      close () {
        this.modalShow = false
      },
      clear () {
        this.search = ''
        this.$nextTick(() => {
          this.$refs.searchInput.$refs.input.focus()
        })
      },
      select(id) {
        this.$emit('submit', id)
        this.close()
      }
    }
  }
</script>
